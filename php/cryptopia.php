<?php

namespace ccxt;

// PLEASE DO NOT EDIT THIS FILE, IT IS GENERATED AND WILL BE OVERWRITTEN:
// https://github.com/ccxt/ccxt/blob/master/CONTRIBUTING.md#how-to-contribute-code

use Exception as Exception; // a common import

class cryptopia extends Exchange {

    public function describe () {
        return array_replace_recursive (parent::describe (), array (
            'id' => 'cryptopia',
            'name' => 'Cryptopia',
            'rateLimit' => 1500,
            'countries' => array ( 'NZ' ), // New Zealand
            'has' => array (
                'CORS' => false,
                'createMarketOrder' => false,
                'fetchClosedOrders' => 'emulated',
                'fetchCurrencies' => true,
                'fetchDepositAddress' => true,
                'fetchMyTrades' => true,
                'fetchTransactions' => false,
                'fetchWithdrawals' => true,
                'fetchDeposits' => true,
                'fetchOHLCV' => true,
                'fetchOrder' => 'emulated',
                'fetchOrderBooks' => true,
                'fetchOrders' => 'emulated',
                'fetchOpenOrders' => true,
                'fetchTickers' => true,
                'deposit' => true,
                'withdraw' => true,
            ),
            'urls' => array (
                'logo' => 'https://user-images.githubusercontent.com/1294454/29484394-7b4ea6e2-84c6-11e7-83e5-1fccf4b2dc81.jpg',
                'api' => array (
                    'public' => 'https://www.cryptopia.co.nz/api',
                    'private' => 'https://www.cryptopia.co.nz/api',
                    'web' => 'https://www.cryptopia.co.nz',
                ),
                'www' => 'https://www.cryptopia.co.nz',
                'referral' => 'https://www.cryptopia.co.nz/Register?referrer=kroitor',
                'doc' => array (
                    'https://support.cryptopia.co.nz/csm?id=kb_article&sys_id=a75703dcdbb9130084ed147a3a9619bc',
                    'https://support.cryptopia.co.nz/csm?id=kb_article&sys_id=40e9c310dbf9130084ed147a3a9619eb',
                ),
            ),
            'timeframes' => array (
                '15m' => 15,
                '30m' => 30,
                '1h' => 60,
                '2h' => 120,
                '4h' => 240,
                '12h' => 720,
                '1d' => 1440,
                '1w' => 10080,
            ),
            'api' => array (
                'web' => array (
                    'get' => array (
                        'Exchange/GetTradePairChart',
                    ),
                ),
                'public' => array (
                    'get' => array (
                        'GetCurrencies',
                        'GetTradePairs',
                        'GetMarkets',
                        'GetMarkets/{id}',
                        'GetMarkets/{hours}',
                        'GetMarkets/{id}/{hours}',
                        'GetMarket/{id}',
                        'GetMarket/{id}/{hours}',
                        'GetMarketHistory/{id}',
                        'GetMarketHistory/{id}/{hours}',
                        'GetMarketOrders/{id}',
                        'GetMarketOrders/{id}/{count}',
                        'GetMarketOrderGroups/{ids}',
                        'GetMarketOrderGroups/{ids}/{count}',
                    ),
                ),
                'private' => array (
                    'post' => array (
                        'CancelTrade',
                        'GetBalance',
                        'GetDepositAddress',
                        'GetOpenOrders',
                        'GetTradeHistory',
                        'GetTransactions',
                        'SubmitTip',
                        'SubmitTrade',
                        'SubmitTransfer',
                        'SubmitWithdraw',
                    ),
                ),
            ),
            'commonCurrencies' => array (
                'ACC' => 'AdCoin',
                'BAT' => 'BatCoin',
                'BEAN' => 'BITB', // rebranding, see issue #3380
                'BLZ' => 'BlazeCoin',
                'BTG' => 'Bitgem',
                'CAN' => 'CanYaCoin',
                'CAT' => 'Catcoin',
                'CC' => 'CCX',
                'CMT' => 'Comet',
                'EPC' => 'ExperienceCoin',
                'FCN' => 'Facilecoin',
                'FT' => 'Fabric Token',
                'FUEL' => 'FC2', // FuelCoin != FUEL
                'HAV' => 'Havecoin',
                'HC' => 'Harvest Masternode Coin', // != HyperCash
                'HSR' => 'HC',
                'KARM' => 'KARMA',
                'LBTC' => 'LiteBitcoin',
                'LDC' => 'LADACoin',
                'MARKS' => 'Bitmark',
                'NET' => 'NetCoin',
                'PLC' => 'Polcoin',
                'RED' => 'RedCoin',
                'STC' => 'StopTrumpCoin',
                'QBT' => 'Cubits',
                'WRC' => 'WarCoin',
            ),
            'options' => array (
                'fetchTickersErrors' => true,
            ),
        ));
    }

    public function fetch_markets ($params = array ()) {
        $response = $this->publicGetGetTradePairs ();
        $result = array ();
        $markets = $response['Data'];
        for ($i = 0; $i < count ($markets); $i++) {
            $market = $markets[$i];
            $numericId = $market['Id'];
            $label = $market['Label'];
            $baseId = $market['Symbol'];
            $quoteId = $market['BaseSymbol'];
            $base = $this->common_currency_code($baseId);
            $quote = $this->common_currency_code($quoteId);
            $symbol = $base . '/' . $quote;
            $id = $baseId . '_' . $quoteId;
            $precision = array (
                'amount' => 8,
                'price' => 8,
            );
            $lot = $market['MinimumTrade'];
            $priceLimits = array (
                'min' => $market['MinimumPrice'],
                'max' => $market['MaximumPrice'],
            );
            $amountLimits = array (
                'min' => $lot,
                'max' => $market['MaximumTrade'],
            );
            $limits = array (
                'amount' => $amountLimits,
                'price' => $priceLimits,
                'cost' => array (
                    'min' => $market['MinimumBaseTrade'],
                    'max' => null,
                ),
            );
            $active = $market['Status'] === 'OK';
            $result[] = array (
                'id' => $id,
                'symbol' => $symbol,
                'numericId' => $numericId,
                'base' => $base,
                'quote' => $quote,
                'baseId' => $baseId,
                'quoteId' => $quoteId,
                'info' => $market,
                'maker' => $market['TradeFee'] / 100,
                'taker' => $market['TradeFee'] / 100,
                'active' => $active,
                'precision' => $precision,
                'limits' => $limits,
                'label' => $label,
            );
        }
        $this->options['marketsByLabel'] = $this->index_by($result, 'label');
        return $result;
    }

    public function fetch_order_book ($symbol, $limit = null, $params = array ()) {
        $this->load_markets();
        $response = $this->publicGetGetMarketOrdersId (array_merge (array (
            'id' => $this->market_id($symbol),
        ), $params));
        $orderbook = $response['Data'];
        return $this->parse_order_book($orderbook, null, 'Buy', 'Sell', 'Price', 'Volume');
    }

    public function fetch_ohlcv ($symbol, $timeframe = '15m', $since = null, $limit = null, $params = array ()) {
        $dataRange = 0;
        if ($since !== null) {
            $dataRanges = array (
                86400,
                172800,
                604800,
                1209600,
                2592000,
                7776000,
                15552000,
            );
            $numDataRanges = is_array ($dataRanges) ? count ($dataRanges) : 0;
            $now = $this->seconds ();
            $sinceSeconds = intval ($since / 1000);
            for ($i = 1; $i < $numDataRanges; $i++) {
                if (($now - $sinceSeconds) > $dataRanges[$i]) {
                    $dataRange = $i;
                }
            }
        }
        $this->load_markets();
        $market = $this->market ($symbol);
        $request = array (
            'tradePairId' => $market['numericId'],
            'dataRange' => $dataRange,
            'dataGroup' => $this->timeframes[$timeframe],
        );
        $response = $this->webGetExchangeGetTradePairChart (array_merge ($request, $params));
        $candles = $response['Candle'];
        $volumes = $response['Volume'];
        for ($i = 0; $i < count ($candles); $i++) {
            $candles[$i][] = $volumes[$i]['basev'];
        }
        return $this->parse_ohlcvs($candles, $market, $timeframe, $since, $limit);
    }

    public function join_market_ids ($ids, $glue = '-') {
        $result = (string) $ids[0];
        for ($i = 1; $i < count ($ids); $i++) {
            $result .= $glue . (string) $ids[$i];
        }
        return $result;
    }

    public function fetch_order_books ($symbols = null, $params = array ()) {
        $this->load_markets();
        if ($symbols === null) {
            throw new ExchangeError ($this->id . ' fetchOrderBooks requires the $symbols argument as of May 2018 (up to 5 $symbols at max)');
        }
        $numSymbols = is_array ($symbols) ? count ($symbols) : 0;
        if ($numSymbols > 5) {
            throw new ExchangeError ($this->id . ' fetchOrderBooks accepts 5 $symbols at max');
        }
        $ids = $this->join_market_ids ($this->market_ids($symbols));
        $response = $this->publicGetGetMarketOrderGroupsIds (array_merge (array (
            'ids' => $ids,
        ), $params));
        $orderbooks = $response['Data'];
        $result = array ();
        for ($i = 0; $i < count ($orderbooks); $i++) {
            $orderbook = $orderbooks[$i];
            $id = $this->safe_string($orderbook, 'Market');
            $symbol = $id;
            if (is_array ($this->markets_by_id) && array_key_exists ($id, $this->markets_by_id)) {
                $market = $this->markets_by_id[$id];
                $symbol = $market['symbol'];
            }
            $result[$symbol] = $this->parse_order_book($orderbook, null, 'Buy', 'Sell', 'Price', 'Volume');
        }
        return $result;
    }

    public function parse_ticker ($ticker, $market = null) {
        $timestamp = $this->milliseconds ();
        $symbol = null;
        if ($market !== null)
            $symbol = $market['symbol'];
        $open = $this->safe_float($ticker, 'Open');
        $last = $this->safe_float($ticker, 'LastPrice');
        $change = $last - $open;
        $baseVolume = $this->safe_float($ticker, 'Volume');
        $quoteVolume = $this->safe_float($ticker, 'BaseVolume');
        $vwap = null;
        if ($quoteVolume !== null)
            if ($baseVolume !== null)
                if ($baseVolume > 0)
                    $vwap = $quoteVolume / $baseVolume;
        return array (
            'symbol' => $symbol,
            'info' => $ticker,
            'timestamp' => $timestamp,
            'datetime' => $this->iso8601 ($timestamp),
            'high' => $this->safe_float($ticker, 'High'),
            'low' => $this->safe_float($ticker, 'Low'),
            'bid' => $this->safe_float($ticker, 'BidPrice'),
            'bidVolume' => null,
            'ask' => $this->safe_float($ticker, 'AskPrice'),
            'askVolume' => null,
            'vwap' => $vwap,
            'open' => $open,
            'close' => $last,
            'last' => $last,
            'previousClose' => null,
            'change' => $change,
            'percentage' => $this->safe_float($ticker, 'Change'),
            'average' => $this->sum ($last, $open) / 2,
            'baseVolume' => $baseVolume,
            'quoteVolume' => $quoteVolume,
        );
    }

    public function fetch_ticker ($symbol, $params = array ()) {
        $this->load_markets();
        $market = $this->market ($symbol);
        $response = $this->publicGetGetMarketId (array_merge (array (
            'id' => $market['id'],
        ), $params));
        $ticker = $response['Data'];
        return $this->parse_ticker($ticker, $market);
    }

    public function fetch_tickers ($symbols = null, $params = array ()) {
        $this->load_markets();
        $response = $this->publicGetGetMarkets ($params);
        $result = array ();
        $tickers = $response['Data'];
        for ($i = 0; $i < count ($tickers); $i++) {
            $ticker = $tickers[$i];
            $id = str_replace ('/', '_', $ticker['Label']);
            $recognized = (is_array ($this->markets_by_id) && array_key_exists ($id, $this->markets_by_id));
            if (!$recognized) {
                if ($this->options['fetchTickersErrors'])
                    throw new ExchangeError ($this->id . ' fetchTickers() returned unrecognized pair $id ' . (string) $id);
            } else {
                $market = $this->markets_by_id[$id];
                $symbol = $market['symbol'];
                $result[$symbol] = $this->parse_ticker($ticker, $market);
            }
        }
        return $this->filter_by_array($result, 'symbol', $symbols);
    }

    public function parse_trade ($trade, $market = null) {
        $timestamp = null;
        if (is_array ($trade) && array_key_exists ('Timestamp', $trade)) {
            $timestamp = $trade['Timestamp'] * 1000;
        } else if (is_array ($trade) && array_key_exists ('TimeStamp', $trade)) {
            $timestamp = $this->parse8601 ($trade['TimeStamp']);
        }
        $price = $this->safe_float($trade, 'Price');
        if (!$price)
            $price = $this->safe_float($trade, 'Rate');
        $cost = $this->safe_float($trade, 'Total');
        $id = $this->safe_string($trade, 'TradeId');
        if ($market === null) {
            $marketId = $this->safe_string($trade, 'Market');
            $marketId = str_replace ('/', '_', $marketId);
            if (is_array ($this->markets_by_id) && array_key_exists ($marketId, $this->markets_by_id)) {
                $market = $this->markets_by_id[$marketId];
            }
        }
        $symbol = null;
        $fee = null;
        if ($market !== null) {
            $symbol = $market['symbol'];
            if (is_array ($trade) && array_key_exists ('Fee', $trade)) {
                $fee = array (
                    'currency' => $market['quote'],
                    'cost' => $trade['Fee'],
                );
            }
        }
        return array (
            'id' => $id,
            'info' => $trade,
            'order' => null,
            'timestamp' => $timestamp,
            'datetime' => $this->iso8601 ($timestamp),
            'symbol' => $symbol,
            'type' => 'limit',
            'side' => strtolower ($trade['Type']),
            'price' => $price,
            'cost' => $cost,
            'amount' => $trade['Amount'],
            'fee' => $fee,
        );
    }

    public function fetch_trades ($symbol, $since = null, $limit = null, $params = array ()) {
        $this->load_markets();
        $market = $this->market ($symbol);
        $hours = 24; // the default
        if ($since !== null) {
            $elapsed = $this->milliseconds () - $since;
            $hour = 1000 * 60 * 60;
            $hours = intval ((int) ceil ($elapsed / $hour));
        }
        $request = array (
            'id' => $market['id'],
            'hours' => $hours,
        );
        $response = $this->publicGetGetMarketHistoryIdHours (array_merge ($request, $params));
        $trades = $response['Data'];
        return $this->parse_trades($trades, $market, $since, $limit);
    }

    public function parse_transaction ($transaction, $currency = null) {
        //
        // fetchWithdrawals
        //
        //     {
        //         Id => 937355,
        //         Currency => 'BTC',
        //         TxId => '5ba7784576cee48bfb9d1524abf7bdade3de65e0f2f9cdd25f7bef2c506cf296',
        //         Type => 'Withdraw',
        //         Amount => 0.7,
        //         Fee => 0,
        //         Status => 'Complete',
        //         Confirmations => 0,
        //         Timestamp => '2017-10-10T18:39:03.8928376',
        //         Address => '14KyZTusAZZGEmZzxsWf4pee7ThtA2iv2E',
        //     }
        //
        // fetchDeposits
        //     {
        //         Id => 7833741,
        //         Currency => 'BCH',
        //         TxId => '0000000000000000011865af4122fe3b144e2cbeea86142e8ff2fb4107352d43',
        //         Type => 'Deposit',
        //         Amount => 0.0003385,
        //         Fee => 0,
        //         Status => 'Confirmed',
        //         Confirmations => 6,
        //         Timestamp => '2017-08-01T16:19:24',
        //         Address => null
        //     }
        //
        $timestamp = $this->parse8601 ($this->safe_string($transaction, 'Timestamp'));
        $code = null;
        $currencyId = $this->safe_string($transaction, 'Currency');
        $currency = $this->safe_value($this->currencies_by_id, $currencyId);
        if ($currency === null) {
            $code = $this->common_currency_code($currencyId);
        }
        if ($currency !== null) {
            $code = $currency['code'];
        }
        $status = $this->safe_string($transaction, 'Status');
        $txid = $this->safe_string($transaction, 'TxId');
        if ($status !== null) {
            $status = $this->parse_transaction_status ($status);
        }
        $id = $this->safe_string($transaction, 'Id');
        $type = $this->parse_transaction_type ($this->safe_string($transaction, 'Type'));
        $amount = $this->safe_float($transaction, 'Amount');
        $address = $this->safe_string($transaction, 'Address');
        $feeCost = $this->safe_float($transaction, 'Fee');
        return array (
            'info' => $transaction,
            'id' => $id,
            'currency' => $code,
            'amount' => $amount,
            'address' => $address,
            'tag' => null,
            'status' => $status,
            'type' => $type,
            'updated' => null,
            'txid' => $txid,
            'timestamp' => $timestamp,
            'datetime' => $this->iso8601 ($timestamp),
            'fee' => array (
                'currency' => $code,
                'cost' => $feeCost,
            ),
        );
    }

    public function parse_transaction_status ($status) {
        $statuses = array (
            'Confirmed' => 'ok',
            'Complete' => 'ok',
            'Pending' => 'pending',
        );
        return $this->safe_string($statuses, $status, $status);
    }

    public function parse_transaction_type ($type) {
        $types = array (
            'Withdraw' => 'withdrawal',
            'Deposit' => 'deposit',
        );
        return $this->safe_string($types, $type, $type);
    }

    public function fetch_transactions_by_type ($type, $code = null, $since = null, $limit = null, $params = array ()) {
        $this->load_markets();
        $request = array (
            'type' => ($type === 'deposit') ? 'Deposit' : 'Withdraw',
        );
        $response = $this->privatePostGetTransactions (array_merge ($request, $params));
        return $this->parseTransactions ($response['Data'], $code, $since, $limit);
    }

    public function fetch_withdrawals ($code = null, $since = null, $limit = null, $params = array ()) {
        return $this->fetch_transactions_by_type ('withdrawal', $code, $since, $limit, $params);
    }

    public function fetch_deposits ($code = null, $since = null, $limit = null, $params = array ()) {
        return $this->fetch_transactions_by_type ('deposit', $code, $since, $limit, $params);
    }

    public function fetch_my_trades ($symbol = null, $since = null, $limit = null, $params = array ()) {
        $this->load_markets();
        $request = array ();
        $market = null;
        if ($symbol !== null) {
            $market = $this->market ($symbol);
            $request['Market'] = $market['id'];
        }
        if ($limit !== null) {
            $request['Count'] = $limit; // default 100
        }
        $response = $this->privatePostGetTradeHistory (array_merge ($request, $params));
        return $this->parse_trades($response['Data'], $market, $since, $limit);
    }

    public function fetch_currencies ($params = array ()) {
        $response = $this->publicGetGetCurrencies ($params);
        $currencies = $response['Data'];
        $result = array ();
        for ($i = 0; $i < count ($currencies); $i++) {
            $currency = $currencies[$i];
            $id = $currency['Symbol'];
            // todo => will need to rethink the fees
            // to add support for multiple withdrawal/deposit methods and
            // differentiated fees for each particular method
            $precision = 8; // default $precision, todo => fix "magic constants"
            $code = $this->common_currency_code($id);
            $active = ($currency['ListingStatus'] === 'Active');
            $status = strtolower ($currency['Status']);
            if ($status !== 'ok')
                $active = false;
            $result[$code] = array (
                'id' => $id,
                'code' => $code,
                'info' => $currency,
                'name' => $currency['Name'],
                'active' => $active,
                'status' => $status,
                'fee' => $currency['WithdrawFee'],
                'precision' => $precision,
                'limits' => array (
                    'amount' => array (
                        'min' => pow (10, -$precision),
                        'max' => pow (10, $precision),
                    ),
                    'price' => array (
                        'min' => pow (10, -$precision),
                        'max' => pow (10, $precision),
                    ),
                    'cost' => array (
                        'min' => $currency['MinBaseTrade'],
                        'max' => null,
                    ),
                    'withdraw' => array (
                        'min' => $currency['MinWithdraw'],
                        'max' => $currency['MaxWithdraw'],
                    ),
                ),
            );
        }
        return $result;
    }

    public function fetch_balance ($params = array ()) {
        $this->load_markets();
        $response = $this->privatePostGetBalance ($params);
        $balances = $response['Data'];
        $result = array ( 'info' => $response );
        for ($i = 0; $i < count ($balances); $i++) {
            $balance = $balances[$i];
            $code = $balance['Symbol'];
            $currency = $this->common_currency_code($code);
            $account = array (
                'free' => $balance['Available'],
                'used' => 0.0,
                'total' => $balance['Total'],
            );
            $account['used'] = $account['total'] - $account['free'];
            $result[$currency] = $account;
        }
        return $this->parse_balance($result);
    }

    public function create_order ($symbol, $type, $side, $amount, $price = null, $params = array ()) {
        if ($type === 'market')
            throw new ExchangeError ($this->id . ' allows limit orders only');
        $this->load_markets();
        $market = $this->market ($symbol);
        // $price = floatval ($price);
        // $amount = floatval ($amount);
        $request = array (
            'Market' => $market['id'],
            'Type' => $this->capitalize ($side),
            // 'Rate' => $this->price_to_precision($symbol, $price),
            // 'Amount' => $this->amount_to_precision($symbol, $amount),
            'Rate' => $price,
            'Amount' => $amount,
        );
        $response = $this->privatePostSubmitTrade (array_merge ($request, $params));
        if (!$response)
            throw new ExchangeError ($this->id . ' createOrder returned unknown error => ' . $this->json ($response));
        $id = null;
        $filled = 0.0;
        $status = 'open';
        if (is_array ($response) && array_key_exists ('Data', $response)) {
            if (is_array ($response['Data']) && array_key_exists ('OrderId', $response['Data'])) {
                if ($response['Data']['OrderId']) {
                    $id = (string) $response['Data']['OrderId'];
                } else {
                    $filled = $amount;
                    $status = 'closed';
                }
            }
        }
        $order = array (
            'id' => $id,
            'timestamp' => null,
            'datetime' => null,
            'lastTradeTimestamp' => null,
            'status' => $status,
            'symbol' => $symbol,
            'type' => $type,
            'side' => $side,
            'price' => $price,
            'cost' => $price * $amount,
            'amount' => $amount,
            'remaining' => $amount - $filled,
            'filled' => $filled,
            'fee' => null,
            // 'trades' => $this->parse_trades($order['trades'], $market),
        );
        if ($id)
            $this->orders[$id] = $order;
        return array_merge (array ( 'info' => $response ), $order);
    }

    public function cancel_order ($id, $symbol = null, $params = array ()) {
        $this->load_markets();
        $response = null;
        try {
            $response = $this->privatePostCancelTrade (array_merge (array (
                'Type' => 'Trade',
                'OrderId' => $id,
            ), $params));
            // We do not know if it is indeed canceled, but cryptopia lacks any
            // reasonable method to get information on executed or canceled order.
            if (is_array ($this->orders) && array_key_exists ($id, $this->orders))
                $this->orders[$id]['status'] = 'canceled';
        } catch (Exception $e) {
            if ($this->last_json_response) {
                $message = $this->safe_string($this->last_json_response, 'Error');
                if ($message) {
                    if (mb_strpos ($message, 'does not exist') !== false)
                        throw new OrderNotFound ($this->id . ' cancelOrder() error => ' . $this->last_http_response);
                }
            }
            throw $e;
        }
        return $this->parse_order($response);
    }

    public function parse_order ($order, $market = null) {
        $symbol = null;
        if ($market !== null) {
            $symbol = $market['symbol'];
        } else if (is_array ($order) && array_key_exists ('Market', $order)) {
            $id = $order['Market'];
            if (is_array ($this->markets_by_id) && array_key_exists ($id, $this->markets_by_id)) {
                $market = $this->markets_by_id[$id];
                $symbol = $market['symbol'];
            } else {
                if (is_array ($this->options['marketsByLabel']) && array_key_exists ($id, $this->options['marketsByLabel'])) {
                    $market = $this->options['marketsByLabel'][$id];
                    $symbol = $market['symbol'];
                }
            }
        }
        $timestamp = $this->parse8601 ($this->safe_string($order, 'TimeStamp'));
        $amount = $this->safe_float($order, 'Amount');
        $remaining = $this->safe_float($order, 'Remaining');
        $filled = null;
        if ($amount !== null && $remaining !== null) {
            $filled = $amount - $remaining;
        }
        $id = $this->safe_value($order, 'OrderId');
        if ($id !== null) {
            $id = (string) $id;
        }
        $side = $this->safe_string($order, 'Type');
        if ($side !== null) {
            $side = strtolower ($side);
        }
        return array (
            'id' => $id,
            'info' => $this->omit ($order, 'status'),
            'timestamp' => $timestamp,
            'datetime' => $this->iso8601 ($timestamp),
            'lastTradeTimestamp' => null,
            'status' => $this->safe_string($order, 'status'),
            'symbol' => $symbol,
            'type' => 'limit',
            'side' => $side,
            'price' => $this->safe_float($order, 'Rate'),
            'cost' => $this->safe_float($order, 'Total'),
            'amount' => $amount,
            'filled' => $filled,
            'remaining' => $remaining,
            'fee' => null,
            // 'trades' => $this->parse_trades($order['trades'], $market),
        );
    }

    public function fetch_orders ($symbol = null, $since = null, $limit = null, $params = array ()) {
        $this->load_markets();
        $market = null;
        $request = array (
            // 'Market' => $market['id'],
            // 'TradePairId' => $market['id'], // Cryptopia identifier (not required if 'Market' supplied)
            // 'Count' => 100, // default = 100
        );
        if ($symbol !== null) {
            $market = $this->market ($symbol);
            $request['Market'] = $market['id'];
        }
        $response = $this->privatePostGetOpenOrders (array_merge ($request, $params));
        $orders = array ();
        for ($i = 0; $i < count ($response['Data']); $i++) {
            $orders[] = array_merge ($response['Data'][$i], array ( 'status' => 'open' ));
        }
        $openOrders = $this->parse_orders($orders, $market);
        for ($j = 0; $j < count ($openOrders); $j++) {
            $this->orders[$openOrders[$j]['id']] = $openOrders[$j];
        }
        $openOrdersIndexedById = $this->index_by($openOrders, 'id');
        $cachedOrderIds = is_array ($this->orders) ? array_keys ($this->orders) : array ();
        $result = array ();
        for ($k = 0; $k < count ($cachedOrderIds); $k++) {
            $id = $cachedOrderIds[$k];
            if (is_array ($openOrdersIndexedById) && array_key_exists ($id, $openOrdersIndexedById)) {
                $this->orders[$id] = array_merge ($this->orders[$id], $openOrdersIndexedById[$id]);
            } else {
                $order = $this->orders[$id];
                if ($order['status'] === 'open') {
                    if (($symbol === null) || ($order['symbol'] === $symbol)) {
                        $this->orders[$id] = array_merge ($order, array (
                            'status' => 'closed',
                            'cost' => $order['amount'] * $order['price'],
                            'filled' => $order['amount'],
                            'remaining' => 0.0,
                        ));
                    }
                }
            }
            $order = $this->orders[$id];
            if (($symbol === null) || ($order['symbol'] === $symbol))
                $result[] = $order;
        }
        return $this->filter_by_since_limit($result, $since, $limit);
    }

    public function fetch_order ($id, $symbol = null, $params = array ()) {
        $id = (string) $id;
        $orders = $this->fetch_orders($symbol, null, null, $params);
        for ($i = 0; $i < count ($orders); $i++) {
            if ($orders[$i]['id'] === $id)
                return $orders[$i];
        }
        throw new OrderNotCached ($this->id . ' order ' . $id . ' not found in cached .orders, fetchOrder requires .orders (de)serialization implemented for this method to work properly');
    }

    public function fetch_open_orders ($symbol = null, $since = null, $limit = null, $params = array ()) {
        $orders = $this->fetch_orders($symbol, $since, $limit, $params);
        $result = array ();
        for ($i = 0; $i < count ($orders); $i++) {
            if ($orders[$i]['status'] === 'open')
                $result[] = $orders[$i];
        }
        return $result;
    }

    public function fetch_closed_orders ($symbol = null, $since = null, $limit = null, $params = array ()) {
        $orders = $this->fetch_orders($symbol, $since, $limit, $params);
        $result = array ();
        for ($i = 0; $i < count ($orders); $i++) {
            if ($orders[$i]['status'] === 'closed')
                $result[] = $orders[$i];
        }
        return $result;
    }

    public function fetch_deposit_address ($code, $params = array ()) {
        $this->load_markets();
        $currency = $this->currency ($code);
        $response = $this->privatePostGetDepositAddress (array_merge (array (
            'Currency' => $currency['id'],
        ), $params));
        $address = $this->safe_string($response['Data'], 'BaseAddress');
        $tag = $this->safe_string($response['Data'], 'Address');
        if ($address !== null) {
            if (strlen ($address) < 1) {
                $address = null;
            }
        }
        if ($address === null) {
            $address = $tag;
            $tag = null;
        }
        $this->check_address($address);
        return array (
            'currency' => $code,
            'address' => $address,
            'tag' => $tag,
            'info' => $response,
        );
    }

    public function withdraw ($code, $amount, $address, $tag = null, $params = array ()) {
        $this->load_markets();
        $currency = $this->currency ($code);
        $this->check_address($address);
        $request = array (
            'Currency' => $currency['id'],
            'Amount' => $amount,
            'Address' => $address, // Address must exist in you AddressBook in security settings
        );
        if ($tag)
            $request['PaymentId'] = $tag;
        $response = $this->privatePostSubmitWithdraw (array_merge ($request, $params));
        return array (
            'info' => $response,
            'id' => $response['Data'],
        );
    }

    public function sign ($path, $api = 'public', $method = 'GET', $params = array (), $headers = null, $body = null) {
        $url = $this->urls['api'][$api] . '/' . $this->implode_params($path, $params);
        $query = $this->omit ($params, $this->extract_params($path));
        if ($api === 'private') {
            $this->check_required_credentials();
            $nonce = (string) $this->nonce ();
            $body = $this->json ($query, array ( 'convertArraysToObjects' => true ));
            $hash = $this->hash ($this->encode ($body), 'md5', 'base64');
            $secret = base64_decode ($this->secret);
            $uri = $this->encode_uri_component($url);
            $lowercase = strtolower ($uri);
            $hash = $this->binary_to_string($hash);
            $payload = $this->apiKey . $method . $lowercase . $nonce . $hash;
            $signature = $this->hmac ($this->encode ($payload), $secret, 'sha256', 'base64');
            $auth = 'amx ' . $this->apiKey . ':' . $this->binary_to_string($signature) . ':' . $nonce;
            $headers = array (
                'Content-Type' => 'application/json',
                'Authorization' => $auth,
            );
        } else {
            if ($query)
                $url .= '?' . $this->urlencode ($query);
        }
        return array ( 'url' => $url, 'method' => $method, 'body' => $body, 'headers' => $headers );
    }

    public function nonce () {
        return $this->milliseconds ();
    }

    public function handle_errors ($code, $reason, $url, $method, $headers, $body, $response) {
        if ($response === null) {
            return; // fallback to default $error handler
        }
        if (is_array ($response) && array_key_exists ('Success', $response)) {
            $success = $this->safe_value($response, 'Success');
            if ($success !== null) {
                if (!$success) {
                    $error = $this->safe_string($response, 'Error');
                    $feedback = $this->id . ' ' . $body;
                    if (gettype ($error) === 'string') {
                        if (mb_strpos ($error, 'Invalid trade amount') !== false) {
                            throw new InvalidOrder ($feedback);
                        }
                        if (mb_strpos ($error, 'No matching trades found') !== false) {
                            throw new OrderNotFound ($feedback);
                        }
                        if (mb_strpos ($error, 'does not exist') !== false) {
                            throw new OrderNotFound ($feedback);
                        }
                        if (mb_strpos ($error, 'Insufficient Funds') !== false) {
                            throw new InsufficientFunds ($feedback);
                        }
                        if (mb_strpos ($error, 'Nonce has already been used') !== false) {
                            throw new InvalidNonce ($feedback);
                        }
                    }
                    throw new ExchangeError ($feedback);
                }
            }
        }
    }
}
