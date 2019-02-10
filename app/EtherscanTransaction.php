<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class EtherscanTransaction extends Model
{
    protected $fillable = ['blockNumber', 'timeStamp', 'hash',
        'blockNumber',
        'timeStamp',
        'hash',
        'nonce',
        'blockHash',
        'transactionIndex',
        'from',
        'to',
        'value',
        'gas',
        'gasPrice',
        'isError',
        'txreceipt_status',
        'input',
        'contractAddress',
        'cumulativeGasUsed',
        'gasUsed',
        'confirmations',
        'token_count'
    ];



}
