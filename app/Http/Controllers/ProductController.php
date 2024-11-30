<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    private $filePath = 'products.json'; // Change to 'products.xml' for XML

    public function index()
    {
        return view('products.index');
    }

    public function store(Request $request)
    {
        $request->validate([
            'productName' => 'required|string|max:255',
            'quantity' => 'required|integer|min:0',
            'price' => 'required|numeric|min:0',
        ]);

        $products = $this->readData();

        $data = [
            'id' => $this->generateUniqueId($products),
            'productName' => $request->input('productName'),
            'quantity' => $request->input('quantity'),
            'price' => $request->input('price'),
            'datetime' => now(),
            'totalValue' => $request->input('quantity') * $request->input('price'),
        ];

        $products[] = $data;
        $this->writeData($products);

        return response()->json(['success' => true, 'data' => $products]);
    }

    public function update(Request $request)
    {
        $request->validate([
            'id' => 'required|integer',
            'productName' => 'required|string|max:255',
            'quantity' => 'required|integer|min:0',
            'price' => 'required|numeric|min:0',
        ]);

        $products = $this->readData();

        foreach ($products as &$product) {
            if ($product['id'] == $request->input('id')) {
                $product['productName'] = $request->input('productName');
                $product['quantity'] = $request->input('quantity');
                $product['price'] = $request->input('price');
                $product['totalValue'] = $request->input('quantity') * $request->input('price');
                $product['datetime'] = now();
                break;
            }
        }

        $this->writeData($products);

        return response()->json(['success' => true, 'data' => $products]);
    }

    public function destroy(Request $request)
    {
        $request->validate([
            'id' => 'required|integer',
        ]);

        $products = $this->readData();
        $products = array_filter($products, function ($product) use ($request) {
            return $product['id'] != $request->input('id');
        });

        $this->writeData($products);

        return response()->json(['success' => true, 'data' => array_values($products)]);
    }

    public function getData()
    {
        $products = $this->readData();
        return response()->json(['success' => true, 'data' => $products]);
    }

    private function readData()
    {
        if (!Storage::exists($this->filePath)) {
            return [];
        }

        $content = Storage::get($this->filePath);
        return json_decode($content, true) ?? [];
    }

    private function writeData($data)
    {
        Storage::put($this->filePath, json_encode($data, JSON_PRETTY_PRINT));
    }

    private function generateUniqueId($products)
    {
        return count($products) > 0 ? max(array_column($products, 'id')) + 1 : 1;
    }
}
