<?php

namespace App\Http\Controllers;

use App\Brands;
use App\Http\Requests\CreateProduct;
use App\Product;
use Illuminate\Support\Facades\Session;
use Request;
use App\Http\Requests;

class ProductController extends Controller
{
    /**
     * Ecommerce-CMS
     *
     * Copyright (C) 2014 - 2015  Tihomir Blazhev.
     *
     * LICENSE
     *
     * Ecommerce-cms is released with dual licensing, using the GPL v3 (license-gpl3.txt) and the MIT license (license-mit.txt).
     * You don't have to do anything special to choose one license or the other and you don't have to notify anyone which license you are using.
     * Please see the corresponding license file for details of these licenses.
     * You are free to use, modify and distribute this software, but all copyright information must remain.
     *
     * @package     ecommerce-cms
     * @copyright   Copyright (c) 2014 through 2015, Tihomir Blazhev
     * @license     http://opensource.org/licenses/MIT  MIT License
     * @version     1.0.0
     * @author      Tihomir Blazhev <raylight75@gmail.com>
     */

    /**
     *
     * Product Class
     *
     * @package ecommerce-cms
     * @category Base Class
     * @author Tihomir Blazhev <raylight75@gmail.com>
     * @link https://raylight75@bitbucket.org/raylight75/ecommerce-cms.git
     */

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
        $products = Product::with('brands')->get();
        $products = Product::paginate(10);
        //echo '<pre>',print_r($products),'</pre>';
        return view('product.index', compact('products'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create()
    {
        $brand  = Brands::lists('brand','brand_id');
        return view('product.create')->with('brand', $brand);
    }

    /**
     * @param CreateProduct $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(CreateProduct $request)
    {
        $data = $this->proccesData($request);
        $product = Product::create($data);
        $product->brands->save($data);
        Session::flash('flash_message', 'Product successfully added!');
        return redirect()->back();
    }

    /**
     * Display the specified resource.
     *
     * @param  int $id
     * @return Response
     */
    public function show($id)
    {
        $product = Product::with('brands')->find($id);
        return view('product.show', compact('product'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int $id
     * @return Response
     */

    public function edit($id)
    {
        $data['brand'] = Brands::lists('brand','brand_id');
        $data['product'] = Product::with('brands')->find($id);
        return view('product.edit',$data);
    }

    /**
     * @param $id
     * @param CreateProduct $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update($id, CreateProduct $request)
    {
        $data = $this->proccesData($request);
        $product = Product::find($id);
        $product->update($data);
        $product->brands->save($data);
        Session::flash('flash_message', 'Product successfully updated!');
        return redirect()->back();
    }

    /**
     * Delete the specified products.
     *
     * @param $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy($id)
    {
        Product::findOrFail($id)->delete();
        Session::flash('flash_message', 'Product successfully deleted!');
        return redirect()->back();
    }

    /**
     * Process uploaded images and request data.
     *
     * @param $request
     * @return mixed
     */
    public function proccesData($request)
    {
        $destinationPath = base_path() . '/public/images/';
        $fileName = $request->file('a_img')->getClientOriginalName();
        $request->file('a_img')->move($destinationPath, $fileName);
        $data = $request->except('a_img','brands');
        $data['brand_id'] = $request->input('brand_id');
        $data['a_img'] = $request->file('a_img')->getClientOriginalName();
        return $data;
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function search()
    {
        $search = Request::get('search');
        $products = Product::where('name', 'like', '%' . $search . '%')
            ->orderBy('name')
            ->paginate(5);
        return view('product.index', compact('products'));
    }
}