<?php

namespace App\Http\Controllers;

use App\Models\SuccessStory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Carbon\Carbon;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class SuccessStoryController extends Controller
{
    /**
     * Get all success stories
     */
    public function index(Request $request): JsonResponse
    {
        $trashed = filter_var($request->get('trashed'), FILTER_VALIDATE_BOOLEAN);

        if ($trashed) {
            $articles = SuccessStory::withTrashed()->get()->toArray();

            return response()->json($articles);
        }
        $articles = SuccessStory::withoutTrashed()->get()->toArray();

        return response()->json($articles);
    }

    /**
     * Create new success story
     */
    public function store(Request $request): Response|JsonResponse
    {
        $this->authorize('create-success-story', SuccessStory::class);

        $request->validate(
            [
                'title'         => ['required', 'max:100', 'unique:success_stories,title'],
                'active'        => ['required', 'bool'],
                'preview_image' => ['string', 'max:255', 'nullable'],
                'industry'      => ['required', 'json'],
                'technologies'  => ['required', 'json'],
                'company'       => ['required', 'json'],
                'steps'         => ['required', 'json'],
                'project'       => ['required', 'json'],
                'similar_cases' => ['json'],
            ]
        );

        try {
            $successStory = new SuccessStory();
            $successStory->fill($request->only(
                [
                    'title',
                    'active',
                    'preview_image',
                    'industry',
                    'technologies',
                    'company',
                    'steps',
                    'project',
                    'similar_cases',
                ]
            )
            );
            $successStory->created_by = $request->user()->id;
            $successStory->updated_by = $request->user()->id;
            $successStory->created_at = Carbon::now();
            $successStory->updated_at = Carbon::now();
            $successStory->save();

            return response()->noContent(201);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    /**
     * Show specific success story
     */
    public function show(int $id): Response|JsonResponse
    {
        try {
            $successStory = SuccessStory::find($id);
        } catch (\Exception $e) {
            throw new NotFoundHttpException('Not found success story with provided id');
        }

        return response()->json($successStory);
    }

    /**
     * Update success story
     */
    public function update(int $id, Request $request): Response|JsonResponse
    {
        $successStory = SuccessStory::findOrFail($id);

        if ($successStory instanceof SuccessStory) {
            $this->authorize('update-success-story', $successStory);

            $request->validate(
                [
                    'title'         => ['max:100', 'unique:vacancies,title'],
                    'active'        => ['bool'],
                    'preview_image' => ['string', 'max:255'],
                    'industry'      => ['json'],
                    'technologies'  => ['json'],
                    'company'       => ['json'],
                    'steps'         => ['json'],
                    'project'       => ['json'],
                    'similar_cases' => ['json'],
                ]
            );

            try {
                $successStory->fill($request->only(
                    [
                        'title',
                        'active',
                        'preview_image',
                        'industry',
                        'technologies',
                        'company',
                        'steps',
                        'project',
                        'similar_cases',
                    ]
                ));
                $successStory->updated_by = $request->user()->id;
                $successStory->updated_at = Carbon::now();
                $successStory->save();

                return response()->noContent();
            } catch (\Exception $e) {
                return response()->json(['error' => $e->getMessage()], 500);
            }
        }

        throw new NotFoundHttpException('Not found success story with provided id');
    }

    /**
     * Permanently delete success story
     */
    public function destroy(int $id): Response|JsonResponse
    {
        $successStory = SuccessStory::findOrFail($id);

        if ($successStory instanceof SuccessStory) {
            $this->authorize('permanently-delete-success-story', $successStory);

            try {
                $successStory->forceDelete();

                return response()->noContent();
            } catch (\Exception $e) {
                return response()->json(['error' => $e->getMessage()], 500);
            }
        }

        throw new NotFoundHttpException('Not found success story with provided id');
    }

    /**
     * Restore success story throw change status
     */
    public function restore(int $id): Response|JsonResponse
    {
        $successStory = SuccessStory::withTrashed()->where('id', $id);

        if ($successStory instanceof SuccessStory) {
            $this->authorize('restore-success-story', $successStory);

            try {
                SuccessStory::withTrashed()->where('id', $id)->restore();

                return response()->noContent();
            } catch (\Exception $e) {
                return response()->json(['error' => $e->getMessage()], 500);
            }
        }

        throw new NotFoundHttpException('Not found success story with provided id');
    }

    /**
     * Delete success story throw change status
     */
    public function delete(int $id): Response|JsonResponse
    {
        $successStory = SuccessStory::findOrFail($id);

        if ($successStory instanceof SuccessStory) {
            $this->authorize('delete-success-story', $successStory);

            try {
                $successStory->delete();

                return response()->noContent();
            } catch (\Exception $e) {
                return response()->json(['error' => $e->getMessage()], 500);
            }
        }

        throw new NotFoundHttpException('Not found success story with provided id');
    }
}
