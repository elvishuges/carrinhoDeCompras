<?php

namespace App\Http\Controllers;

use App\Pedido;
use App\Produto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\PedidoProduto;
use App\CupomDesconto;

class CarrinhoController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth'); // todas as requisições passa por aqui, verificando se esta logado
        // se sim, redireciona para a pagina que o usuário queria ir.
    }

    public function index()
    {
        $pedidos = Pedido::where([
            'status' => 'RE',
            'user_id' => Auth::id(),

        ])->get();

        return view('carrinho.index', compact('pedidos'));
    }

    public function adicionar()
    {

        $this->middleware('VerifyCsrfToken');

        $req = Request();
        $idproduto = $req->input('id');

        $produto = Produto::find($idproduto); // recebe 
        if( empty($produto->id) ) { // verifica se encontrou o produto
            // exibe mensagem temporaria na view
            $req->session()->flash('mensagem-falha', 'Produto não encontrado em nossa loja!');
            return redirect()->route('carrinho.index');
        }

        $idusuario = Auth::id();

        $idpedido = Pedido::consultaId([
            'user_id' => $idusuario,
            'status'  => 'RE' // Reservada
            ]);

        if( empty($idpedido) ) {
            $pedido_novo = Pedido::create([
                'user_id' => $idusuario,
                'status'  => 'RE'
                ]);

            $idpedido = $pedido_novo->id;
        }

        PedidoProduto::create([
            'pedido_id'  => $idpedido,
            'produto_id' => $idproduto,
            'valor'      => $produto->valor,
            'status'     => 'RE'
            ]);

        $req->session()->flash('mensagem-sucesso', 'Produto adicionado ao carrinho com sucesso!');

        return redirect()->route('carrinho.index');

    }


    

    public function remover()
    {

        $this->middleware('VerifyCsrfToken'); // validando() se o token é valido )

        $req = Request();
        $idpedido           = $req->input('pedido_id');
        $idproduto          = $req->input('produto_id');
        $remove_apenas_item = (boolean)$req->input('item');
        $idusuario          = Auth::id();

        $idpedido = Pedido::consultaId([
            'id'      => $idpedido,
            'user_id' => $idusuario,
            'status'  => 'RE' // Reservada
            ]);

        if( empty($idpedido) ) {
            $req->session()->flash('mensagem-falha', 'Pedido não encontrado!');
            return redirect()->route('carrinho.index');
        }

        $where_produto = [
            'pedido_id'  => $idpedido,
            'produto_id' => $idproduto
        ];
        
        // desc = do maior para o menor. Vamo remover o ultimo item inserido
        // o first pega o primeiro registro encontrado
        $produto = PedidoProduto::where($where_produto)->orderBy('id', 'desc')->first();
        if( empty($produto->id) ) {
            $req->session()->flash('mensagem-falha', 'Produto não encontrado no carrinho!');
            return redirect()->route('carrinho.index');
        }

        if($remove_apenas_item ) {
            $where_produto['id'] = $produto->id;
        }
        
        PedidoProduto::where($where_produto)->delete();

        $check_pedido = PedidoProduto::where([
            'pedido_id' => $produto->pedido_id
            ])->exists(); // exist retorna um booleano

        if( !$check_pedido ) {
            Pedido::where([
                'id' => $produto->pedido_id
                ])->delete();
        }

        $req->session()->flash('mensagem-sucesso', 'Produto removido do carrinho com sucesso!');

        return redirect()->route('carrinho.index');
    }


    public function concluir()
    {
        $this->middleware('VerifyCsrfToken');

        $req = Request();
        $idpedido  = $req->input('pedido_id');
        $idusuario = Auth::id();

        $check_pedido = Pedido::where([
            'id'      => $idpedido,
            'user_id' => $idusuario,
            'status'  => 'RE' // Reservada
            ])->exists();

        if( !$check_pedido ) {
            $req->session()->flash('mensagem-falha', 'Pedido não encontrado!');
            return redirect()->route('carrinho.index');
        }

        $check_produtos = PedidoProduto::where([
            'pedido_id' => $idpedido
            ])->exists();
        if(!$check_produtos) {
            $req->session()->flash('mensagem-falha', 'Produtos do pedido não encontrados!');
            return redirect()->route('carrinho.index');
        }

        PedidoProduto::where([
            'pedido_id' => $idpedido
            ])->update([
                'status' => 'PA'
            ]);
        Pedido::where([
                'id' => $idpedido
            ])->update([
                'status' => 'PA'
            ]);

        $req->session()->flash('mensagem-sucesso', 'Compra concluída com sucesso!');

        return redirect()->route('carrinho.compras');
    }

    public function compras()
    {

        $compras = Pedido::where([
            'status'  => 'PA',
            'user_id' => Auth::id()
            ])->orderBy('created_at', 'desc')->get();
        
        return $compras; // retorna em formato json

        // $cancelados = Pedido::where([
        //     'status'  => 'CA',
        //     'user_id' => Auth::id()
        //     ])->orderBy('updated_at', 'desc')->get();

        // return view('carrinho.compras', compact('compras', 'cancelados'));

        }


}
