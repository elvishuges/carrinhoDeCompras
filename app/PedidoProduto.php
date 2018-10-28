<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PedidoProduto extends Model
{   
    protected $fillable =[
        'pedido_id',
        'produto_id',
        'status',
        'valor'
    ];

    public function produto()
    {   // aqui é pesquisado na tabela produto,quais produtos prertencem a esse pedidoProduto
        // usando produto_id e encontrando o id da tabela produto
        return $this->belongsTo('App\Produto', 'produto_id', 'id');
    }
}
