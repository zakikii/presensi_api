<?php

namespace App\Http\Controllers;

use App\Models\Slider;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class SliderController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $slider = Slider::all();
        return view('slider.daftarslider', compact('slider'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('slider.buatslider');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $code = Str::random(5);
        $slider = new Slider();
        $slider->judul_slider = $request->input('judul_slider');
        $slider->detail_slider = $request->input('detail_slider');
        // $slider->kode_kelas = $code;
        $slider->gambar_slider = "";
        if ($slider->save()) {
            $photo = $request->file('gambar_slider');
            if ($photo != null) {
                $ext = $photo->getClientOriginalExtension();
                $fileName = rand(10000, 50000) . '.' . $ext;
                if ($ext == 'jpg' || $ext == 'png') {
                    if ($photo->move(public_path(), $fileName)) {
                        $slider = Slider::find($slider->id);
                        $slider->gambar_slider = url('/') . '/' . $fileName;
                        $slider->save();
                    }
                }
            }
            return redirect()->back()->with('success', 'Slider information inserted successfully!');
        }
        return redirect()->back()->with('failed', 'Slider information could not insert!');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Slider  $slider
     * @return \Illuminate\Http\Response
     */
    public function show(Slider $slider)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Slider  $slider
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $slider = Slider::find($id);
        return view('slider.editslider', compact('slider'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Slider  $slider
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $slider = Slider::find($id);
        $slider->judul_slider = $request->input('judul_slider');
        $slider->detail_slider = $request->input('detail_slider');
        $slider->gambar_slider = "";
        if ($slider->save()) {
            $photo = $request->file('gambar_slider');
            if ($photo != null) {
                $ext = $photo->getClientOriginalExtension();
                $fileName = rand(10000, 50000) . '.' . $ext;
                if ($ext == 'jpg' || $ext == 'png') {
                    if ($photo->move(public_path(), $fileName)) {
                        $slider = Slider::find($slider->id);
                        $slider->gambar_slider = url('/') . '/' . $fileName;
                        $slider->save();
                    }
                }
            }
            return redirect()->back()->with('success', 'Slider information updated successfully!');
        }
        return redirect()->back()->with('failed', 'Slider information could not updated!');
    }


    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Slider  $slider
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        if (Slider::destroy($id)) {
            return redirect()->back()->with('deleted', 'slider berhasil dihapus');
        }
        return redirect()->back()->with('delete-failed', 'slider tidak dapat dihapus');
    }
}
