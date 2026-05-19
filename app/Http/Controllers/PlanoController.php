<?php

class PlanoController extends Controller{

    public function index(){
        
    }

    public function store(Request $request){

    }

    public function update(Request $request, Plano $plano){

        return redirect()->back();
    }

    public function destroy(Plano $plano){
        $plano->delete();
        return redirect()->back();
    }
}