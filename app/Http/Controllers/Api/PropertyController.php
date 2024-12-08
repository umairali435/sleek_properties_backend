<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Property; 
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
class PropertyController extends Controller
{
    
    public function store(Request $request)
    {

        try { 
        
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'address' => 'required|string|max:255',
            'description' => 'required|string',
            'price' => 'required|numeric',
            'bedrooms' => 'required|integer',
            'bathrooms' => 'required|integer',
            'sqft' => 'nullable|integer',
            'property_type' => 'required|string|max:100',
            'status' => 'required|string|in:available,sold,pending',
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        
        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors(),
            ], 422);
        }

        $imagePath = $request->file('image')->store('property_images', 'public');
        $property = Property::create([
            'title' => $request->title,
            'address' => $request->address,
            'description' => $request->description,
            'price' => $request->price,
            'bedrooms' => $request->bedrooms,
            'bathrooms' => $request->bathrooms,
            'sqft' => $request->sqft,
            'property_type' => $request->property_type,
            'status' => $request->status,
            'image' => $imagePath,
        ]);
        

        
        return response()->json([
            'message' => 'Property added successfully.',
            'property' => $property,
        ], 201);
    } catch (\Exception $e) {
        
        Log::error('Error storing property: ' . $e->getMessage());
        
        
        return response()->json(['error' => 'Internal Server Error', 'message' => $e->getMessage()], 500);
    }
    }
    public function index()
    {
        try {
            $properties = Property::all();
            return response()->json($properties);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Something went wrong, please try again later.'], 500);
        }
    }

    /**
     * Display the specified property.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        
        $property = Property::findOrFail($id); 
        
        
        return new PropertyResource($property);
    }
    
    public function update(Request $request, $id)
    {
        try {
        
        
        
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'address' => 'required|string',
            'description' => 'required|string',
            'price' => 'required|numeric',
            'bedrooms' => 'required|integer',
            'bathrooms' => 'required|integer',
            'sqft' => 'nullable|integer',
            'property_type' => 'required|string',
            'status' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors(),
            ], 422);
        }

        
        $property = Property::find($id);

        if (!$property) {
            return response()->json(['error' => 'Property not found'], 404);
        }

        
        $property->title = $request->title;
        $property->address = $request->address;
        $property->description = $request->description;
        $property->price = $request->price;
        $property->bedrooms = $request->bedrooms;
        $property->bathrooms = $request->bathrooms;
        $property->sqft = $request->sqft;
        $property->property_type = $request->property_type;
        $property->status = $request->status ?: $property->status;

        
        if ($request->hasFile('image')) {
            
            if ($property->image && Storage::exists('public/' . $property->image)) {
                Storage::delete('public/' . $property->image);
            }

            
            $imagePath = $request->file('image')->store('property_images', 'public');
            $property->image = $imagePath;
        }

        
        $property->save();

        
        $imageUrl = Storage::url($property->image);

        
        return response()->json([
            'property' => $property,
            'image_url' => $imageUrl,
        ], 200);

    } catch (\Exception $e) {
        
        Log::error('Error storing property: ' . $e->getMessage());
        
        
        return response()->json(['error' => 'Internal Server Error', 'message' => $e->getMessage()], 500);
    }
    }  

    public function destroy($id)
{
    // Find the property by ID
    $property = Property::find($id);

    if (!$property) {
        return response()->json(['error' => 'Property not found'], 404);
    }

    // Delete the image from storage if it exists
    if ($property->image && Storage::exists('public/' . $property->image)) {
        Storage::delete('public/' . $property->image);
    }

    // Delete the property from the database
    $property->delete();

    return response()->json(['message' => 'Property deleted successfully'], 200);
}
}
