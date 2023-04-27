<?php

namespace App\Http\Controllers;

use App\Models\Article;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class BlogController extends Controller
{
    /**
     * Get all blog articles
     */
    public function index(Request $request): JsonResponse
    {
        $trashed = filter_var($request->get('trashed'), FILTER_VALIDATE_BOOLEAN);

        if ($trashed) {
            $articles = Article::withTrashed()->get()->toArray();

            return response()->json($articles);
        }
        $articles = Article::withoutTrashed()->get()->toArray();

        return response()->json($articles);
    }

    /**
     * Create new vacancy
     */
    public function store(Request $request): Response|JsonResponse
    {
        $this->authorize('create-article', Article::class);

        $request->validate(
            [
                'title'             => ['required', 'max:100', 'unique:vacancies,title'],
                'active'            => ['required', 'bool'],
                'announcement_text' => ['required', 'string'],
                'detail_text'       => ['string'],
                'img_src'           => ['nullable', 'string', 'max:255'],
                'is_important'      => ['required', 'bool'],
                'release_date'      => ['required', 'date'],
            ]
        );

        try {
            $article = new Article();
            $article->fill($request->only(
                [
                    'title',
                    'active',
                    'announcement_text',
                    'detail_text',
                    'img_src',
                    'is_important',
                    'release_date',
                ]
            )
            );
            $article->created_by = $request->user()->id;
            $article->updated_by = $request->user()->id;
            $article->created_at = Carbon::now();
            $article->updated_at = Carbon::now();
            $article->save();

            return response()->noContent(201);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    /**
     * Show specific article
     */
    public function show(int $id): Response|JsonResponse
    {
        $article = Article::find($id);

        return response()->json($article);
    }

    /**
     * Update vacancy
     */
    public function update(int $id, Request $request): Response|JsonResponse
    {
        $article = Article::findOrFail($id);

        if ($article instanceof Article) {
            $this->authorize('update-article', $article);

            $request->validate(
                [
                    'title'             => ['max:100', 'unique:vacancies,title'],
                    'active'            => ['bool'],
                    'announcement_text' => ['string'],
                    'detail_text'       => ['string'],
                    'img_src'           => ['nullable', 'string', 'max:255'],
                    'is_important'      => ['bool'],
                    'release_date'      => ['date'],
                ]
            );

            try {
                $article->fill($request->only(
                    [
                        'title',
                        'active',
                        'announcement_text',
                        'detail_text',
                        'img_src',
                        'is_important',
                        'release_date',
                    ]
                ));
                $article->updated_by = $request->user()->id;
                $article->updated_at = Carbon::now();
                $article->save();

                return response()->noContent();
            } catch (\Exception $e) {
                return response()->json(['error' => $e->getMessage()], 500);
            }
        }

        throw new NotFoundHttpException('Not found article with provided id');
    }

    /**
     * Permanently delete blog
     */
    public function destroy(int $id): Response|JsonResponse
    {
        $article = Article::findOrFail($id);

        if ($article instanceof Article) {
            $this->authorize('permanently-delete-article', $article);

            try {
                $article->forceDelete();

                return response()->noContent();
            } catch (\Exception $e) {
                return response()->json(['error' => $e->getMessage()], 500);
            }
        }

        throw new NotFoundHttpException('Not found article with provided id');
    }

    /**
     * Restore blog throw change status
     */
    public function restore(int $id): Response|JsonResponse
    {
        $article = Article::withTrashed()->where('id', $id);

        if ($article instanceof Article) {
            $this->authorize('restore-article', $article);

            try {
                Article::withTrashed()->where('id', $id)->restore();

                return response()->noContent();
            } catch (\Exception $e) {
                return response()->json(['error' => $e->getMessage()], 500);
            }
        }

        throw new NotFoundHttpException('Not found article with provided id');
    }

    /**
     * Delete blog throw change status
     */
    public function delete(int $id): Response|JsonResponse
    {
        $article = Article::findOrFail($id);

        if ($article instanceof Article) {
            $this->authorize('delete-article', $article);

            try {
                $article->delete();

                return response()->noContent();
            } catch (\Exception $e) {
                return response()->json(['error' => $e->getMessage()], 500);
            }
        }

        throw new NotFoundHttpException('Not found article with provided id');
    }
}
