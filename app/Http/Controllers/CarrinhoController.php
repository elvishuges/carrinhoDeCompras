<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Pedido;

class CarrinhoController extends Controller
{
    function __construct(){
        $this->middleware('auth'); // todas as requisições passa por aqui, verificando se esta logado
        // se sim, redireciona para a pagina que o usuário queria ir.
    }

    public function index(){

        $pedido = Pedido ::where([
             'status' => 'RE',
              'user_id' => Auth::id()

        ])-> get();
        
            //   dd([ //  acessanso o model usa-se isso
            //      $pedidos,
            //       $pedidos[0]->pedido_produtos,
            //       $pedidos[0]->pedido_produtos[0]->produtos->nome,

            // ]);

        return view('carrinho.index',compact('pedido'));
    }

}
