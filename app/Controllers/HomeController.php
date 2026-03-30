<?php

namespace App\Controllers;

use ZQuery\Mapper\IdentityMap;
use App\Mapper\UserMapper;
use App\Repository\UserRepository;
use \App\Entity\Model;

class HomeController
{

    public function __construct() {}

    public function index($slug = null)
    {
        return view("pages.home", [
            "title" => $slug ?? "Ziro",
        ]);
    }

    public function dashboard($id = null)
    {
        $identityMap = new IdentityMap();
        $zq = Model::zquery();
        $mapper = new UserMapper($zq->getConnection(), $zq->getGrammar(), $identityMap);
        $repo = new UserRepository($mapper);

        $user = $repo->find(1);
        $all = $repo->findAll();

        $user->fill(['status' => 'disabled']);
        $repo->save($user);

        // $repo->delete($user);
        // print_r($user);
        // exit;

        return view("pages.dashboard", [
            "one" => print_r($user, true),
            "title" => $id ?? "Dashboard",
        ]);
    }

    public function login()
    {
        return view(
            "pages.login",
            ["title" => "Login", "header" => "Login to your account"],
            'layouts/auth'
        );
    }
}
