# -*- coding: utf-8 -*-

# PLEASE DO NOT EDIT THIS FILE, IT IS GENERATED AND WILL BE OVERWRITTEN:
# https://github.com/ccxt/ccxt/blob/master/CONTRIBUTING.md#how-to-contribute-code

from ccxt.async_support.base.exchange import Exchange
from ccxt.base.errors import ExchangeError


class coinmate(Exchange):

    def describe(self):
        return self.deep_extend(super(coinmate, self).describe(), {
            'id': 'coinmate',
            'name': 'CoinMate',
            'countries': ['GB', 'CZ', 'EU'],  # UK, Czech Republic
            'rateLimit': 1000,
            'has': {
                'CORS': True,
                'fetchMyTrades': True,
                'fetchTransactions': True,
            },
            'urls': {
                'logo': 'https://user-images.githubusercontent.com/1294454/27811229-c1efb510-606c-11e7-9a36-84ba2ce412d8.jpg',
                'api': 'https://coinmate.io/api',
                'www': 'https://coinmate.io',
                'fees': 'https://coinmate.io/fees',
                'doc': [
                    'https://coinmate.docs.apiary.io',
                    'https://coinmate.io/developers',
                ],
                'referral': 'https://coinmate.io?referral=YTFkM1RsOWFObVpmY1ZjMGREQmpTRnBsWjJJNVp3PT0',
            },
            'requiredCredentials': {
                'apiKey': True,
                'secret': True,
                'uid': True,
            },
            'api': {
                'public': {
                    'get': [
                        'orderBook',
                        'ticker',
                        'transactions',
                        'tradingPairs',
                    ],
                },
                'private': {
                    'post': [
                        'balances',
                        'bitcoinCashWithdrawal',
                        'bitcoinCashDepositAddresses',
                        'bitcoinDepositAddresses',
                        'bitcoinWithdrawal',
                        'bitcoinWithdrawalFees',
                        'buyInstant',
                        'buyLimit',
                        'cancelOrder',
                        'cancelOrderWithInfo',
                        'createVoucher',
                        'dashDepositAddresses',
                        'dashWithdrawal',
                        'ethereumWithdrawal',
                        'ethereumDepositAddresses',
                        'litecoinWithdrawal',
                        'litecoinDepositAddresses',
                        'openOrders',
                        'order',
                        'orderHistory',
                        'pusherAuth',
                        'redeemVoucher',
                        'replaceByBuyLimit',
                        'replaceByBuyInstant',
                        'replaceBySellLimit',
                        'replaceBySellInstant',
                        'rippleDepositAddresses',
                        'rippleWithdrawal',
                        'sellInstant',
                        'sellLimit',
                        'transactionHistory',
                        'traderFees',
                        'tradeHistory',
                        'transfer',
                        'transferHistory',
                        'unconfirmedBitcoinDeposits',
                        'unconfirmedBitcoinCashDeposits',
                        'unconfirmedDashDeposits',
                        'unconfirmedEthereumDeposits',
                        'unconfirmedLitecoinDeposits',
                        'unconfirmedRippleDeposits',
                    ],
                },
            },
            'fees': {
                'trading': {
                    'maker': 0.05 / 100,
                    'taker': 0.15 / 100,
                },
            },
        })

    async def fetch_markets(self, params={}):
        response = await self.publicGetTradingPairs(params)
        #
        #     {
        #         "error":false,
        #         "errorMessage":None,
        #         "data": [
        #             {
        #                 "name":"BTC_EUR",
        #                 "firstCurrency":"BTC",
        #                 "secondCurrency":"EUR",
        #                 "priceDecimals":2,
        #                 "lotDecimals":8,
        #                 "minAmount":0.0002,
        #                 "tradesWebSocketChannelId":"trades-BTC_EUR",
        #                 "orderBookWebSocketChannelId":"order_book-BTC_EUR",
        #                 "tradeStatisticsWebSocketChannelId":"statistics-BTC_EUR"
        #             },
        #         ]
        #     }
        #
        data = self.safe_value(response, 'data')
        result = []
        for i in range(0, len(data)):
            market = data[i]
            id = self.safe_string(market, 'name')
            baseId = self.safe_string(market, 'firstCurrency')
            quoteId = self.safe_string(market, 'secondCurrency')
            base = self.safe_currency_code(baseId)
            quote = self.safe_currency_code(quoteId)
            symbol = base + '/' + quote
            result.append({
                'id': id,
                'symbol': symbol,
                'base': base,
                'quote': quote,
                'baseId': baseId,
                'quoteId': quoteId,
                'active': None,
                'info': market,
                'precision': {
                    'price': self.safe_integer(market, 'priceDecimals'),
                    'amount': self.safe_integer(market, 'lotDecimals'),
                },
                'limits': {
                    'amount': {
                        'min': self.safe_float(market, 'minAmount'),
                        'max': None,
                    },
                    'price': {
                        'min': None,
                        'max': None,
                    },
                    'cost': {
                        'min': None,
                        'max': None,
                    },
                },
            })
        return result

    async def fetch_balance(self, params={}):
        await self.load_markets()
        response = await self.privatePostBalances(params)
        balances = self.safe_value(response, 'data')
        result = {'info': response}
        currencyIds = list(balances.keys())
        for i in range(0, len(currencyIds)):
            currencyId = currencyIds[i]
            code = self.safe_currency_code(currencyId)
            balance = self.safe_value(balances, currencyId)
            account = self.account()
            account['free'] = self.safe_float(balance, 'available')
            account['used'] = self.safe_float(balance, 'reserved')
            account['total'] = self.safe_float(balance, 'balance')
            result[code] = account
        return self.parse_balance(result)

    async def fetch_order_book(self, symbol, limit=None, params={}):
        await self.load_markets()
        request = {
            'currencyPair': self.market_id(symbol),
            'groupByPriceLimit': 'False',
        }
        response = await self.publicGetOrderBook(self.extend(request, params))
        orderbook = response['data']
        timestamp = self.safe_timestamp(orderbook, 'timestamp')
        return self.parse_order_book(orderbook, timestamp, 'bids', 'asks', 'price', 'amount')

    async def fetch_ticker(self, symbol, params={}):
        await self.load_markets()
        request = {
            'currencyPair': self.market_id(symbol),
        }
        response = await self.publicGetTicker(self.extend(request, params))
        ticker = self.safe_value(response, 'data')
        timestamp = self.safe_timestamp(ticker, 'timestamp')
        last = self.safe_float(ticker, 'last')
        return {
            'symbol': symbol,
            'timestamp': timestamp,
            'datetime': self.iso8601(timestamp),
            'high': self.safe_float(ticker, 'high'),
            'low': self.safe_float(ticker, 'low'),
            'bid': self.safe_float(ticker, 'bid'),
            'bidVolume': None,
            'ask': self.safe_float(ticker, 'ask'),
            'vwap': None,
            'askVolume': None,
            'open': None,
            'close': last,
            'last': last,
            'previousClose': None,
            'change': None,
            'percentage': None,
            'average': None,
            'baseVolume': self.safe_float(ticker, 'amount'),
            'quoteVolume': None,
            'info': ticker,
        }

    async def fetch_transactions(self, code=None, since=None, limit=None, params={}):
        await self.load_markets()
        request = {
            'limit': 1000,
        }
        if limit is not None:
            request['limit'] = limit
        if since is not None:
            request['timestampFrom'] = since
        if code is not None:
            request['currency'] = self.currencyId(code)
        response = await self.privatePostTransferHistory(self.extend(request, params))
        items = response['data']
        return self.parse_transactions(items, None, since, limit)

    def parse_transaction_status(self, status):
        statuses = {
            # any other types ?
            'COMPLETED': 'ok',
        }
        return self.safe_string(statuses, status, status)

    def parse_transaction(self, item, currency=None):
        #
        # deposits
        #
        #     {
        #         transactionId: 1862815,
        #         timestamp: 1516803982388,
        #         amountCurrency: 'LTC',
        #         amount: 1,
        #         fee: 0,
        #         walletType: 'LTC',
        #         transferType: 'DEPOSIT',
        #         transferStatus: 'COMPLETED',
        #         txid:
        #         'ccb9255dfa874e6c28f1a64179769164025329d65e5201849c2400abd6bce245',
        #         destination: 'LQrtSKA6LnhcwRrEuiborQJnjFF56xqsFn',
        #         destinationTag: None
        #     }
        #
        # withdrawals
        #
        #     {
        #         transactionId: 2140966,
        #         timestamp: 1519314282976,
        #         amountCurrency: 'EUR',
        #         amount: 8421.7228,
        #         fee: 16.8772,
        #         walletType: 'BANK_WIRE',
        #         transferType: 'WITHDRAWAL',
        #         transferStatus: 'COMPLETED',
        #         txid: None,
        #         destination: None,
        #         destinationTag: None
        #     }
        #
        timestamp = self.safe_integer(item, 'timestamp')
        amount = self.safe_float(item, 'amount')
        fee = self.safe_float(item, 'fee')
        txid = self.safe_string(item, 'txid')
        address = self.safe_string(item, 'destination')
        tag = self.safe_string(item, 'destinationTag')
        currencyId = self.safe_string(item, 'amountCurrency')
        code = self.safe_currency_code(currencyId, currency)
        type = self.safe_string_lower(item, 'transferType')
        status = self.parse_transaction_status(self.safe_string(item, 'transferStatus'))
        id = self.safe_string(item, 'transactionId')
        return {
            'id': id,
            'timestamp': timestamp,
            'datetime': self.iso8601(timestamp),
            'currency': code,
            'amount': amount,
            'type': type,
            'txid': txid,
            'address': address,
            'tag': tag,
            'status': status,
            'fee': {
                'cost': fee,
                'currency': currency,
            },
            'info': item,
        }

    async def fetch_my_trades(self, symbol=None, since=None, limit=None, params={}):
        await self.load_markets()
        if limit is None:
            limit = 1000
        request = {
            'limit': limit,
        }
        if since is not None:
            request['timestampFrom'] = since
        response = await self.privatePostTradeHistory(self.extend(request, params))
        items = response['data']
        return self.parse_trades(items, None, since, limit)

    def parse_trade(self, trade, market=None):
        #
        # fetchMyTrades(private)
        #
        #     {
        #         transactionId: 2671819,
        #         createdTimestamp: 1529649127605,
        #         currencyPair: 'LTC_BTC',
        #         type: 'BUY',
        #         orderType: 'LIMIT',
        #         orderId: 101810227,
        #         amount: 0.01,
        #         price: 0.01406,
        #         fee: 0,
        #         feeType: 'MAKER'
        #     }
        #
        # fetchTrades(public)
        #
        #     {
        #         "timestamp":1561598833416,
        #         "transactionId":"4156303",
        #         "price":10950.41,
        #         "amount":0.004,
        #         "currencyPair":"BTC_EUR",
        #         "tradeType":"BUY"
        #     }
        #
        symbol = None
        marketId = self.safe_string(trade, 'currencyPair')
        quote = None
        if marketId is not None:
            if marketId in self.markets_by_id[marketId]:
                market = self.markets_by_id[marketId]
                quote = market['quote']
            else:
                baseId, quoteId = marketId.split('_')
                base = self.safe_currency_code(baseId)
                quote = self.safe_currency_code(quoteId)
                symbol = base + '/' + quote
        if symbol is None:
            if market is not None:
                symbol = market['symbol']
        price = self.safe_float(trade, 'price')
        amount = self.safe_float(trade, 'amount')
        cost = None
        if amount is not None:
            if price is not None:
                cost = price * amount
        side = self.safe_string_lower_2(trade, 'type', 'tradeType')
        type = self.safe_string_lower(trade, 'orderType')
        orderId = self.safe_string(trade, 'orderId')
        id = self.safe_string(trade, 'transactionId')
        timestamp = self.safe_integer_2(trade, 'timestamp', 'createdTimestamp')
        fee = None
        feeCost = self.safe_float(trade, 'fee')
        if feeCost is not None:
            fee = {
                'cost': feeCost,
                'currency': quote,
            }
        takerOrMaker = self.safe_string(trade, 'feeType')
        takerOrMaker = 'maker' if (takerOrMaker == 'MAKER') else 'taker'
        return {
            'id': id,
            'info': trade,
            'timestamp': timestamp,
            'datetime': self.iso8601(timestamp),
            'symbol': symbol,
            'type': type,
            'side': side,
            'order': orderId,
            'takerOrMaker': takerOrMaker,
            'price': price,
            'amount': amount,
            'cost': cost,
            'fee': fee,
        }

    async def fetch_trades(self, symbol, since=None, limit=None, params={}):
        await self.load_markets()
        market = self.market(symbol)
        request = {
            'currencyPair': market['id'],
            'minutesIntoHistory': 10,
        }
        response = await self.publicGetTransactions(self.extend(request, params))
        #
        #     {
        #         "error":false,
        #         "errorMessage":None,
        #         "data":[
        #             {
        #                 "timestamp":1561598833416,
        #                 "transactionId":"4156303",
        #                 "price":10950.41,
        #                 "amount":0.004,
        #                 "currencyPair":"BTC_EUR",
        #                 "tradeType":"BUY"
        #             }
        #         ]
        #     }
        #
        data = self.safe_value(response, 'data', [])
        return self.parse_trades(data, market, since, limit)

    async def create_order(self, symbol, type, side, amount, price=None, params={}):
        await self.load_markets()
        method = 'privatePost' + self.capitalize(side)
        request = {
            'currencyPair': self.market_id(symbol),
        }
        if type == 'market':
            if side == 'buy':
                request['total'] = amount  # amount in fiat
            else:
                request['amount'] = amount  # amount in fiat
            method += 'Instant'
        else:
            request['amount'] = amount  # amount in crypto
            request['price'] = price
            method += self.capitalize(type)
        response = await getattr(self, method)(self.extend(request, params))
        return {
            'info': response,
            'id': str(response['data']),
        }

    async def cancel_order(self, id, symbol=None, params={}):
        return await self.privatePostCancelOrder({'orderId': id})

    def nonce(self):
        return self.milliseconds()

    def sign(self, path, api='public', method='GET', params={}, headers=None, body=None):
        url = self.urls['api'] + '/' + path
        if api == 'public':
            if params:
                url += '?' + self.urlencode(params)
        else:
            self.check_required_credentials()
            nonce = str(self.nonce())
            auth = nonce + self.uid + self.apiKey
            signature = self.hmac(self.encode(auth), self.encode(self.secret))
            body = self.urlencode(self.extend({
                'clientId': self.uid,
                'nonce': nonce,
                'publicKey': self.apiKey,
                'signature': signature.upper(),
            }, params))
            headers = {
                'Content-Type': 'application/x-www-form-urlencoded',
            }
        return {'url': url, 'method': method, 'body': body, 'headers': headers}

    async def request(self, path, api='public', method='GET', params={}, headers=None, body=None):
        response = await self.fetch2(path, api, method, params, headers, body)
        if 'error' in response:
            if response['error']:
                raise ExchangeError(self.id + ' ' + self.json(response))
        return response
