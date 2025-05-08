<?php

namespace App\Http\Controllers;

use App\Models\Sheet;
use App\Models\SheetImage;
use App\Services\OssService;
use Illuminate\Http\Request;


class SheetController extends Controller
{

    public function index()
    {
        //EAGER load :D :) :D
        $sheets = Sheet::with('images', 'comments')->where('user_id', auth()->id())->get();
        return view('sheets.index', ['sheets' => $sheets]);
    }

    public function show($sheetId)
    {
        // Find the sheet by ID
        $sheet = Sheet::with('images', 'comments')->findOrFail($sheetId);
        return view('sheets.show', ['sheet' => $sheet]);
    }

    public function createPage(Request $request)
    {
        //currently in construction
        //if id is passed, we are editing an existing sheet. also verify that the user is the owner of the sheet
        if ($request->has('id')) {
            $sheetId = $request->input('id');
            $sheet = Sheet::with('images')->findOrFail($sheetId);//hopefully we can find this sheet

            // Check if the authenticated user is the owner of the sheet
            if ($sheet->user_id !== auth()->id()) {
                return redirect()->route('sheets.index')->with('error', 'You do not have permission to edit this sheet.');
            }

            return view('sheets.create', ['sheet' => $sheet]);
        }
        return view('sheets.create');
    }

    public function create(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'is_public' => 'required',//can be on/off
            'preview_audio_path' => 'nullable|string',
            'image_paths' => 'required|array|min:1',
            'image_paths.*' => 'required|string',
        ]);

        // Create the Music Sheet itself
        $sheet = new Sheet();
        $sheet->user_id = auth()->id();
        $sheet->title = $request->input('title');
        $sheet->description = $request->input('description');
        $sheet->is_public = $request->input('is_public');
        $sheet->preview_audio_path = $request->input('preview_audio_path');
        $sheet->save();

        // Create ALL associated SheetImage records
        foreach ($request->input('image_paths') as $path) {
            $image = new SheetImage();
            $image->sheet_id = $sheet->id;
            $image->image_path = $path;
            $image->save();
        }

        //redirect to my sheets page with success message
        return redirect()->route('sheets.index')->with('success', 'Sheet created successfully!');
    }

    public function update(Request $request)
    {
        $request->validate([
            'id' => 'required|integer|exists:sheets,id',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'preview_audio_path' => 'nullable|string',
            'image_paths' => 'required|array|min:1',
            'image_paths.*' => 'required|string',
        ]);

        // Find the sheet by ID
        $sheet = Sheet::with('images')->findOrFail($request->input('id'));

        // Check if the authenticated user is the owner of the sheet
        if ($sheet->user_id !== auth()->id()) {
            return redirect()->route('sheets.index')->with('error', 'You do not have permission to edit this sheet.');
        }

        // Update the Music Sheet itself
        $sheet->title = $request->input('title');
        $sheet->description = $request->input('description');
        $sheet->is_public = $request->input('is_public') ?? 0; // Default to 0 if not provided
        $sheet->preview_audio_path = $request->input('preview_audio_path');
        $sheet->save();

        // See if we need to delete/add any images
        // Get the current image paths
        $currentImagePaths = $sheet->images()->pluck('image_path')->toArray();
        $newImagePaths = $request->input('image_paths');// Get the new image paths from the request
        $imagesToDelete = array_diff($currentImagePaths, $newImagePaths);// Find images to delete
        $imagesToAdd = array_diff($newImagePaths, $currentImagePaths);// Find images to add
        // Delete images that are no longer in the new list
        foreach ($imagesToDelete as $path) {
            $image = $sheet->images()->where('image_path', $path)->first();
            if ($image) {
                $image->delete();
            }
        }
        // Add new images
        foreach ($imagesToAdd as $path) {
            $image = new SheetImage();
            $image->sheet_id = $sheet->id;
            $image->image_path = $path;
            $image->save();
        }

        // Delete images not needed from oss
        if (count($imagesToDelete) != 0) {
            $oss = new OssService();
            foreach ($imagesToDelete as $path) {
                $oss->delete($path);
            }
        }


        //redirect to my sheets page with success message
        return redirect()->route('sheets.index')->with('success', 'Sheet updated successfully!');
    }

    public function delete(Request $request)
    {
        // Validate the request
        $request->validate([
            'id' => 'required|integer|exists:sheets,id',
        ]);
        $sheetId = $request->input('id');
        // Find the sheet by ID
        $sheet = Sheet::findOrFail($sheetId);
        // Check if the authenticated user is the owner of the sheet
        if ($sheet->user_id !== auth()->id()) {
            return redirect()->route('sheets.index')->with('error', 'You do not have permission to delete this sheet.');
        }
        //delete all comments associated with the sheet
        $sheet->comments()->delete();
        // Delete the sheet and its associated images
        $sheet->images()->delete();
        $sheet->delete();
        return redirect()->route('sheets.index')->with('success', 'Sheet deleted successfully!');
    }


}
