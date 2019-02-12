<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\EtherscanTransaction;
use kornrunner\Ethereum\Transaction;
use App\JSON_RPC;
use App\Transaction as TX;

class TokenController extends Controller
{
    public function getTransactions() {
        set_time_limit(22000);

        $transaction = EtherscanTransaction::orderBy('blockNumber', 'DESC')->first();
        $start_block = is_null($transaction) ? 0 : $transaction->blockNumber + 1;

        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, 'https://api.etherscan.io/api?module=account&action=txlistinternal&address='.env('ADDRESS1').'&startblock='.$start_block.'&endblock=latest&sort=asc&apikey='.env('ETHERSCAN_APIKEY'));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
        $ret = json_decode(curl_exec($ch), true);


        foreach($ret['result'] as $r) {

            try {
                $transaction = EtherscanTransaction::where('hash', $r['hash'])->firstOrFail();
            }
            catch (\Exception $e) {
                $transaction = new EtherscanTransaction();
            }

            $transaction->fill($r);
            $transaction->save();


        }


        curl_setopt($ch, CURLOPT_URL, 'https://api.etherscan.io/api?module=account&action=txlistinternal&address='.env('ADDRESS2').'&startblock='.$start_block.'&endblock=latest&sort=asc&apikey='.env('ETHERSCAN_APIKEY'));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
        $ret = json_decode(curl_exec($ch), true);


        foreach($ret['result'] as $r) {

            try {
                $transaction = EtherscanTransaction::where('hash', $r['hash'])->firstOrFail();
            }
            catch (\Exception $e) {
                $transaction = new EtherscanTransaction();
            }

            $transaction->fill($r);
            $transaction->save();
        }
    }


    function sendTokens() {
        set_time_limit(60000);

        $rpc = new JSON_RPC();
        $chainID = env('CHAIN_ID', 4);

        $gasPrice = $rpc->request('eth_gasPrice');
        $gasPrice = $gasPrice['result'];

        $gasLimit = '0x249F0';
        $to       = env('TOKEN');
        $value    = '0';

        $addressTo = $rpc->createAddrParam(env('CONTRACT'));

        $privateKey = env('PRIVATE_KEY1');
        $address = env('ADDRESS1');

        $nonce = $rpc->request('eth_getTransactionCount', $address, 'latest');
        $nonce['result'] = $nonce['result'] == '0x0' ? '' : $nonce['result'];
        $nonce = hexdec($nonce['result']);

        $transactions = EtherscanTransaction::where('to', $address)
                                            ->where('status',0)
                                            ->where('value', '<>', '0')->get();

        $result = array();
        foreach ($transactions as $tx) {

            $val = gmp_init($tx->value);
            $val = gmp_strval($val, 16);
            $val = $rpc->createParam($val);

            $data     = '0xcae9ca51' . $addressTo . $val . '00000000000000000000000000000000000000000000000000000000000000600000000000000000000000000000000000000000000000000000000000000000';

            $n = $nonce == 0 ? '' : '0x'.dechex($nonce);

            $transaction = new Transaction($n, $gasPrice, $gasLimit, $to, $value, $data);
            $rawTx = $transaction->getRaw($privateKey, $chainID);

            $res = $rpc->request('eth_sendRawTransaction','0x'.$rawTx);

            if (isset($res['error'])) {
                $result[] = $res;
            }

            if(isset($res['result']) &&  preg_match('/[a-f0-9]+/', $res['result'])) {
                $trans = new TX();
                $trans->hash = $res['result'];
                $trans->save();
                $tx->status = 1;
                $tx->save();
                $result[] = $res['result'];
            }

            $nonce++;
        }







        $privateKey = env('PRIVATE_KEY2');
        $address = env('ADDRESS2');

        $nonce = $rpc->request('eth_getTransactionCount', $address, 'latest');
        $nonce['result'] = $nonce['result'] == '0x0' ? '' : $nonce['result'];
        $nonce = hexdec($nonce['result']);

        $transactions = EtherscanTransaction::where('to', $address)
            ->where('status',0)
            ->where('value', '<>', '0')->get();

        $result = array();
        foreach ($transactions as $tx) {

            $val = gmp_init($tx->value);
            $val = gmp_strval($val, 16);
            $val = $rpc->createParam($val);

            $data     = '0xcae9ca51' . $addressTo . $val . '00000000000000000000000000000000000000000000000000000000000000600000000000000000000000000000000000000000000000000000000000000000';

            $n = $nonce == 0 ? '' : '0x'.dechex($nonce);

            $transaction = new Transaction($n, $gasPrice, $gasLimit, $to, $value, $data);
            $rawTx = $transaction->getRaw($privateKey, $chainID);

            $res = $rpc->request('eth_sendRawTransaction','0x'.$rawTx);

            if (isset($res['error'])) {
                $result[] = $res;
            }

            if(isset($res['result']) && preg_match('/[a-f0-9]+/', $res['result'])) {
                $trans = new TX();
                $trans->hash = $res['result'];
                $trans->save();
                $tx->status = 1;
                $tx->save();
                $result[] = $res['result'];
            }

            $nonce++;
        }

    }


    public function refund($val)
    {
        set_time_limit(60000);

        $rpc = new JSON_RPC();
        $chainID = env('CHAIN_ID', 4);

        $gasPrice = $rpc->request('eth_gasPrice');
        $gasPrice = $gasPrice['result'];

        $gasLimit = '0x493E0';
        $to       = env('CONTRACT');
        $value    = '0';

        $privateKey = env('PRIVATE_KEY1');
        $address = env('ADDRESS1');

        $nonce = $rpc->request('eth_getTransactionCount', $address, 'latest');
        $nonce = $nonce['result'] == '0x0' ? '' : $nonce['result'];

        $val = gmp_init($val);
        $val = gmp_strval($val, 16);
        $val = $rpc->createParam($val);

        $data     = '0x278ecde1'.$val;

        $transaction = new Transaction($nonce, $gasPrice, $gasLimit, $to, $value, $data);
        $rawTx = $transaction->getRaw($privateKey, $chainID);

        $res = $rpc->request('eth_sendRawTransaction','0x'.$rawTx);

        if (isset($res['error'])) {

        }

        if(isset($res['result']) && preg_match('/[a-f0-9]+/', $res['result'])) {
            $tx = new TX();
            $tx->hash = $res['result'];
            $tx->save();
        }

        return response()->json($res);
    }
}
