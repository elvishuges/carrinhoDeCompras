<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Pedido extends Model
{

    protected $fillable = [
        'user_id',
        'status',
    ];
    /**
    em DB::row, ele traria toda a raw.
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

    public function pedido_produtos_itens()
    {
        return $this->hasMany('App\PedidoProduto');
    }

    // recece um array que é passado pro where do Model Pedido
    public static function consultaId($where)
    {
        // Retorna primeiro registro que comtem as informacoes
        // self diz que abusca é no proprio model
        $pedido = self::where($where)->first(['id']); //nesse caso queremos apenas o id
        return !empty($pedido->id) ? $pedido->id : null;
    }
}
