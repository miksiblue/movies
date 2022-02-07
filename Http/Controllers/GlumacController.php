<?php

namespace App\Http\Controllers;

use App\Models\Film;
use App\Models\Genre;
use App\Models\Glumac;
use Illuminate\Http\Request;
use Intervention\Image\Facades\Image;

class GlumacController extends Controller
{
    //

    public function index()
    {
        $glumci = Glumac::all();
        return view('glumac/index', compact('glumci'));

    }

    public function show(Glumac $glumac)
    {
        $glumci = Glumac::all();
        return view('glumac/show', compact('glumac', 'glumci'));
    }

    public function create(Glumac $glumac)
    {
        $glumci = Glumac::all();
        return view('glumac/create', compact('glumac','glumci'));
    }

    public function store()
    {
        $glumac = Glumac::create($this->validateRequests());
        $this->storeImage($glumac);

        return redirect('glumac/index');
    }


    public function edit(Glumac $glumac)
    {
        return view('glumac/edit' , compact('glumac'));
    }

    public function update(Glumac $glumac, Request $request)
    {
        $data = \request()->validate([
            'ime' => 'required',

        ]);

        $glumac->update($data);
        return back();
    }

    public function destroy(Glumac $glumac)
    {
        $glumac->delete();
        return redirect('glumac/index');
    }

    public function validateRequests()
    {
        return \request()->validate([

            'ime' => 'required',
            'biografija' => 'required',
            'slika' => 'sometimes|file|image|max:5000',
            'datum_rodjenja' => 'required'
        ]);
    }

    public function storeImage($glumac)
    {
        if (\request()->has('slika')) {
            $glumac->update([
                'slika' => \request()->slika->store('upolads', 'public'),
            ]);

            $image = Image::make(public_path('storage/' . $glumac->slika))->fit(3000, 30, null, 'top-left');
            $image->save();
        }
    }
}
