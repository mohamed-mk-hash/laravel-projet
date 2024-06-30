<?php

namespace App\Http\Controllers\Admin;

use Image;
use App\Models\ProductImage;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\File;

class ProductImageController extends Controller
{
    public function update(Request $request){
        $image = $request->image;

        // Get the original filename
        $originalName = $image->getClientOriginalName();

        $productImage = new ProductImage();
        $productImage->product_id = $request->product_id;
        $productImage->image = $originalName; // Save the original name
        $productImage->save();

        $imageName = $originalName; // Use the original name

        // Generate Product Thumbnail
        // Large Image
        $destPath = public_path().'/uploads/product/large/'.$imageName;
        $imageInstance = Image::make($image->getPathName());
        $imageInstance->resize(1400, null, function ($constraint) {
            $constraint->aspectRatio();
        });
        $imageInstance->save($destPath);

        // Small Image
        $destPath = public_path().'/uploads/product/small/'.$imageName;
        $imageInstance = Image::make($image->getPathName());
        $imageInstance->fit(300, 300);
        $imageInstance->save($destPath);

        return response()->json([
            'status' => true,
            'image_id' => $productImage->id,
            'ImagePath' => asset('uploads/product/small/'.$productImage->image),
            'message' => 'Image Saved successfully',
        ]);
    }

    public function destroy(Request $request){
        $productImage = ProductImage::find($request->id);
        
        if(empty($productImage)){
            return response()->json([
                'status' => false,
                'message' => 'Image not Found',
            ]);
        }

        //Delete Images From Folder
        File::delete(public_path('uploads/product/large/'.$productImage->image));
        File::delete(public_path('uploads/product/small/'.$productImage->image));

        $productImage->delete();

        return response()->json([
            'status' => true,
            'message' => 'Image Deleted successfully',
        ]);
    }
}
