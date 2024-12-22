<?php

namespace App\Http\Controllers;

use App\Models\Property;
use Illuminate\Http\Request;

class propertyController extends Controller
{

    public function index()
    {
        $properties = Property::paginate(10);
        return view('property.index', compact('properties'));
    }

    public function create()
    {
        return view('property.create');
    }
}
