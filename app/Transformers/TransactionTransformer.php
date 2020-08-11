<?php

namespace App\Transformers;

use App\Transaction;
use League\Fractal\TransformerAbstract;

class TransactionTransformer extends TransformerAbstract
{
  /**
   * List of resources to automatically include
   *
   * @var array
   */
  protected $defaultIncludes = [
    //
  ];

  /**
   * List of resources possible to include
   *
   * @var array
   */
  protected $availableIncludes = [
    //
  ];

  /**
   * A Fractal transformer.
   *
   * @return array
   */
  public function transform(Transaction $transaction)
  {
    return [
      'identifier' => (int)$transaction->id,
      'quantity' => (int)$transaction->quantity,
      'buyer' => (int)$transaction->buyer_id,
      'seller' => (int)$transaction->seller_id,
      'product' => (int)$transaction->product_id,
      'createdDate' => (string)$transaction->created_at,
      'lastChanged' => (string)$transaction->updated_at,
      'deletedDate' => isset($transaction->deleted_at) ? (string)$transaction->deleted_at : null,
    ];
  }
}
