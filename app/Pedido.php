<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Pedido extends Model
{    

    protected $fillable = [
        'user_id',
        'status'
    ];
    /**
    em DB::raw, ele trarria toda a raw.
    Mas informamos que queremos determinada informação da raw
     */
    public function pedido_produtos()
    {
        return $this->hasMany('App\PedidoProduto')
            ->select(\DB::raw('produto_id,sum(desconto) as descontos,sum(valor) as valores,
        count(1) as qtd'))
            ->groupBy('produto_id') // toas informaçoes acima agrupada por produto adicionado ao carrinho
            ->orderBy('produto_id', 'desc'); // os ultimos produtos criados ficam primeiro em nosso carrinho
            // (ordenados)
    }
}
