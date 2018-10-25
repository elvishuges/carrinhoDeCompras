<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class CarrinhoController extends Controller
{
    function __construct(){
        $this->middleware('auth'); // todas as requisições passa por aqui, verificando se esta logado
        // se sim, redireciona para a pagina que o usuário queria ir.
    }

    public function index(){

        $pedidos = App\Pedidos ::where([
             'status ' => 'RE',
              'user_id' => Auth::id()

        ])-> get();

        return view('carrinho.index',compact('pedidos'));
    }

}
