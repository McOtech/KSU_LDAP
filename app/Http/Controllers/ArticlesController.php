<?php

// namespace App\Http\Controllers;

// use App\Models\Article;
// use Illuminate\Http\Request;
// use Illuminate\Support\Facades\Crypt;
// use Illuminate\Support\Str;

// class ArticlesController extends Controller
// {
//     /**
//      * Display a listing of the resource.
//      *
//      * @return \Illuminate\Http\Response
//      */
//     public function index()
//     {
//         try {
//             $articles = json_encode(Article::all());
//             return response()->json(json_decode($articles), 200);
//         } catch (\Throwable $th) {
//             return response()->json(['message' => $th->getMessage()], 400);
//         }
//     }

//     /**
//      * Store a newly created resource in storage.
//      *
//      * @param  \Illuminate\Http\Request  $request
//      * @return \Illuminate\Http\Response
//      */
//     public function store(Request $request)
//     {
//         $user = $request->session()->get('user');

//         try {
//             // Input Validation
//             $article = $request->validate([
//                 'title' => ['required', 'string', 'max:150'],
//                 'banner' => ['required', 'image']
//             ]);
//         } catch (\Throwable $th) {
//             return response()->json(['message' => $th->getMessage()], 400);
//         }

//         try {
//             // Process the banner image
//             $bannerFolder = Str::slug($article['title']);
//             $bannerUrl = $request->file('banner')->store('public/articles/' . $bannerFolder);
//             if ($bannerUrl) {
//                 $article['banner'] = $bannerUrl;
//                 // dd(Crypt::encrypt($bannerUrl));
//             } else {
//                 return response()->json(['error' => 'Image upload failed.'], 400);
//             }

//             // Store new Article
//             $newArticle = Article::create([
//                 'title' => $article['title'],
//                 'banner' => $article['banner'],
//                 'user_id' => $user['id']
//             ]);

//             // Return an object of the created Article
//             return response()->json([
//                 'id' => $newArticle['id'],
//                 'title' => $newArticle['title'],
//                 'banner' => $newArticle['banner'],
//                 'user_id' => $newArticle['user_id'],
//             ], 201);

//         } catch (\Throwable $th) {
//             // Get error message
//             $errorMessage = $th->getMessage();

//             // return user friendly message
//             return response()->json(['error' => 'Error processing you inputs please try again.'], 400);
//         }
//     }

//     /**
//      * Display the specified resource.
//      *
//      * @param  int  $id
//      * @return \Illuminate\Http\Response
//      */
//     public function show($id)
//     {
//         try {
//             $article = json_encode(Article::where('id', $id)->first());
//             return response()->json(json_decode($article), 200);
//         } catch (\Throwable $th) {
//             return response()->json(['message' => $th->getMessage()], 400);
//         }
//     }

//     /**
//      * Update the specified resource in storage.
//      *
//      * @param  \Illuminate\Http\Request  $request
//      * @param  int  $id
//      * @return \Illuminate\Http\Response
//      */
//     public function update(Request $request, $id)
//     {
//         //
//     }

//     /**
//      * Remove the specified resource from storage.
//      *
//      * @param  int  $id
//      * @return \Illuminate\Http\Response
//      */
//     public function destroy($id)
//     {
//         //
//     }
// }
