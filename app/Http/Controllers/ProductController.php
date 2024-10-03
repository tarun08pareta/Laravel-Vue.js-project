<?php

namespace App\Http\Controllers;

use Exception;
use App\Models\Product;
use Illuminate\Http\Request;
use App\Imports\ProductImport;
use App\Exports\ProductsExport;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use App\Services\ImageUploadService;
use Maatwebsite\Excel\Facades\Excel;
use App\Http\Resources\ProductResource;
use App\Services\ProductCategoryService;
use Illuminate\Support\Facades\Validator;
use App\Http\Requests\StoreProductRequest;
use App\Services\Product\ProductServiceImpl;
use App\Exports\ProductExport;

class ProductController extends Controller
{
    private const PRODUCT_CREATED_SUCCESSFULLY = 'Product created successfully';
    private const DEFAULT_IMAGE_URL = '/images/default-image.jpg';
    protected ProductServiceImpl $productServiceImpl;
    protected ProductCategoryService $productCategoryService;


    public function __construct(ProductServiceImpl $productServiceImpl)
    {
        $this->productServiceImpl = $productServiceImpl;
        // $this->productCategoryService = $productCategoryService;
    }

    public function index(): JsonResponse
    {
        $result = ['status' => 200];
        try {
            $result['products'] = Product::with('categories')->get();;
            // $result['products'] = ProductResource::collection($products);
        } catch (Exception $e) {
            $result = [
                'status' => 500,
                'error' => $e->getMessage()
            ];
        }
        return response()->json($result, $result['status']);
        // Fetch all products
        // $products = Product::with('categories')->get();

        // If you're using a resource, convert it to a JSON response
        // return response()->json(ProductResource::collection($products));
    }

    public function getProductsByCategory(int $categoryId): JsonResponse
    {
        $result = ['status' => 200];
        $products = $this->productServiceImpl->getProductsByCategory($categoryId);
        $result['products'] = $products;
        return response()->json($result, $result['status']);
    }


    // public function getProductsByCategory(Request $request, int $categoryId): JsonResponse
    // {
    //     $result = ['status' => 200];

    //     try {
    //         // Get the filtering parameters from the request
    //         // $price = $request->input('price');          // e.g. minimum price
    //         // $discount = $request->input('discount');    // e.g. minimum discount
    //         $conditionId = $request->input('condition_id');  // Filter by condition
    //         // $name = $request->input('name');            // Filter by name

    //         // Start building the query
    //         $query = Product::whereHas('categories', function ($query) use ($categoryId) {
    //             $query->where('category_id', $categoryId);
    //         });

    //         // Apply filters if they are present

    //         if ($conditionId) {
    //             $query->where('condition_id', $conditionId);
    //         }



    //         // Fetch the filtered products
    //         $products = $query->get();

    //         // Format the result using ProductResource if necessary
    //         $result['products'] = ProductResource::collection($products);
    //     } catch (Exception $e) {
    //         $result = [
    //             'status' => 500,
    //             'error' => $e->getMessage(),
    //         ];
    //     }

    //     return response()->json($result, $result['status']);
    // }









    /**
     * @throws Exception
     */
    public function store(StoreProductRequest $request): JsonResponse
    {
        $product = $this->createProduct($request);
        $category_id = $request->input('category_id');
        $createdProduct = $this->productServiceImpl
            ->addProduct($product, $category_id);
        return response()->json([
            'message' => self::PRODUCT_CREATED_SUCCESSFULLY,
            'createdProduct' => $createdProduct
        ], 201);
    }

    protected function createProduct(StoreProductRequest $request): Product
    {
        if ($request->hasFile('image')) {
            $path = ImageUploadService::uploadImage($request->file('image'));
            $image = $path;
        } else {
            $image = config('app.url') . self::DEFAULT_IMAGE_URL;
        }

        $price = $request->input('price');
        $discount = $request->input('discount', 0);
        $finalPrice = $price - ($price * $discount / 100);

        return new Product([
            'name' => $request->input('name'),
            'description' => $request->input('description'),
            'price' => $price,
            'discount' => $discount,
            'final_price' => $finalPrice,
            'image' => $image,
            'condition_id' => $request->input('condition_id'), // Ensure condition_id is saved
            'status' => $request->input('status', 1),

        ]);
    }

    public function getProductById(int $id)
    {
        $product = Product::with('categories')->find($id);
        $result['product'] = new ProductResource($product);
        return new ProductResource($product);
        // if ($product) {
        //     return response()->json($product);
        // } else {
        //     return response()->json(['error' => 'Product not found'], 404);
        // }


        // $result = ['status' => 200];
        // try {
        //     $product = Product::with('categories')->find($id);
        //     $result['product'] = new ProductResource($product);

        //     if ($product) {
        //         $result['product'] = $product;
        //     } else {
        //         $result = [
        //             'status' => 404,
        //             'message' => 'Product not found',
        //         ];
        //     }
        // } catch (Exception $e) {
        //     $result = [
        //         'status' => 500,
        //         'error' => $e->getMessage(),
        //     ];
        // }
        // return response()->json($result, $result['status']);

    }


    public function update(Request $request, int $id): JsonResponse
    {
        // $product = Product::find($id);
        $product =  Product::with('categories')->find($id);

        if (!$product) {
            return response()->json([
                'status' => 404,
                'message' => 'Product not found'
            ], 404);
        }

        // Define validation rules
        // $validator = Validator::make($request->all(), [
        //     'name' => 'required|string|max:255',
        //     'description' => 'nullable|string',
        //     'price' => 'required|numeric|min:0',
        //     'discount' => 'nullable|numeric|min:0|max:100',
        //     'category_id' => 'nullable|exists:categories,id',
        //     'condition_id' => 'nullable|exists:conditions,id',
        //     'image' => 'nullable',
        // ]);

        // if ($validator->fails()) {
        //     return response()->json([
        //         'status' => 422,
        //         'errors' => $validator->errors()
        //     ], 422);
        // }

        // Update the product fields
        $product->name = $request->input('name', $product->name);
        $product->description = $request->input('description', $product->description);
        $product->price = $request->input('price', $product->price);
        $product->discount = $request->input('discount', $product->discount);
        $product->condition_id = $request->input('condition_id', $product->condition_id);
        $product->status = $request->input('status', $product->status);
        // Handle image upload
        if ($request->hasFile('image')) {
            $path = ImageUploadService::uploadImage($request->file('image'));
            $product->image = $path;
        }

        // Calculate the final price
        $product->final_price = $product->price - ($product->price * $product->discount / 100);

        try {
            $product->save();
            return response()->json([
                'product' => new ProductResource($product)
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 500,
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function destroy($id): JsonResponse
    {
        $product = Product::find($id);

        if (!$product) {
            return response()->json([
                'status' => 404,
                'message' => 'Product not found',
            ], 404);
        }

        try {
            $product->delete();
            return response()->json([
                'status' => 200,
                'message' => 'Product deleted successfully',
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'status' => 500,
                'error' => $e->getMessage(),
            ], 500);
        }
    }
    public function getProductsByCondition(int $conditionId): JsonResponse
    {
        $result = ['status' => 200];

        try {
            // Fetch products that match the given condition_id
            $products = Product::where('condition_id', $conditionId)->get();

            // If you have a ProductResource, use it to format the response
            $result['products'] = ProductResource::collection($products);
        } catch (Exception $e) {
            $result = [
                'status' => 500,
                'error' => $e->getMessage(),
            ];
        }

        return response()->json($result, $result['status']);
    }

    public function getFilteredProducts(Request $request): JsonResponse
    {
        $result = ['status' => 200];

        try {
            // Start building the query
            $query = Product::query();
            // dd($query->toSql());
            // Apply condition filter if present
            if ($request->has('condition_id') && $request->condition_id !== null) {
                $query->where('condition_id', $request->condition_id);
            }


            // Apply category filter if present
            if ($request->has('category_id') && $request->category_id !== null) {
                $query->whereHas('categories', function ($q) use ($request) {
                    $q->where('category_id', $request->category_id);
                });
            }
            // dd($query->toSql());

            // Apply sorting by price if present
            if ($request->has('price_order') && $request->price_order !== null) {
                $query->orderBy('price', $request->price_order);
            }
            if ($request->filled('name')) {
                $query->where('name', 'like', '%' . $request->name . '%');
            }

            $perPage = $request->input('perPage', 4);  // Default is 10 per page
            $products = $query->paginate($perPage);

            // Execute the query and get the results
            // $products = $query->get();
            // Calculate pagination details
            $nextPageUrl = $products->currentPage() < $products->lastPage()
                ? $request->url() . '?page=' . ($products->currentPage() + 1)
                : null;
            $prevPageUrl = $products->currentPage() > 1
                ? $request->url() . '?page=' . ($products->currentPage() - 1)
                : null;
            // Use a resource to format the response if you have one
            $result['products'] = ProductResource::collection($products);
            $result['pagination'] = [
                'total' => $products->total(),
                'per_page' => $products->perPage(),
                'current_page' => $products->currentPage(),
                'last_page' => $products->lastPage(),
                'from' => $products->firstItem(),
                'to' => $products->lastItem(),
                'next_page_url' => $nextPageUrl,
                'prev_page_url' => $prevPageUrl,
            ];
        } catch (Exception $e) {
            $result = [
                'status' => 500,
                'error' => $e->getMessage(),
            ];
        }

        return response()->json($result, $result['status']);
    }

    public function import(Request $request)
    {
        // Validate file input
        $request->validate([
            'file' => 'required',
        ]);

        // Import products from the uploaded file
        Excel::import(new ProductImport, $request->file('file'));

        return response()->json(['message' => 'Products imported successfully']);
    }

    public function export(Request $request)
    {
        $ids = $request->query('ids'); // Get the selected product IDs
        $idsArray = explode(',', $ids);

        // Generate the file name
        $fileName = 'products_export.xlsx';

        // Export products based on the selected IDs
        return Excel::download(new ProductExport($idsArray), $fileName);
    }


}
