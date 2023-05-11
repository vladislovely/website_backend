<?php

namespace App\Http\Controllers;

use App\Models\Vacancy;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class VacancyController extends Controller
{
    /**
     * Get all vacancies
     */
    public function index(Request $request): JsonResponse
    {
        $trashed = filter_var($request->get('trashed'), FILTER_VALIDATE_BOOLEAN);

        if ($trashed) {
            $vacancies = Vacancy::withTrashed()->get()->toArray();

            return response()->json($vacancies);
        }
        $vacancies = Vacancy::withoutTrashed()->get()->toArray();

        return response()->json($vacancies);
    }

    /**
     * Create new vacancy
     */
    public function store(Request $request): Response|JsonResponse
    {
        $this->authorize('create-vacancy', Vacancy::class);

        $request->validate(
            [
                'title'              => ['required', 'max:100', 'unique:vacancies,title'],
                'active'             => ['required', 'bool'],
                'announcement_text'  => ['required', 'string'],
                'detail_text'        => ['nullable', 'string'],
                'description'        => ['required', 'string'],
                'conditions'         => ['required', 'json'],
                'locations'          => ['required', 'json', 'nullable'],
                'language_level'     => ['required', 'nullable', 'json'],
                'grade'              => ['required', 'nullable', 'json'],
                'country'            => ['required', 'nullable', 'json'],
                'remote_format'      => ['required', 'bool'],
                'technologies'       => ['required', 'json'],
                'specialisations'    => ['required', 'json'],
                'offer_timeline'     => ['required', 'json', 'nullable'],
                'vacancy_type'       => ['required', 'json'],
                'work_schedule'      => ['required', 'json'],
                'type_of_employment' => ['required', 'json'],
                'work_experience'    => ['required', 'json'],
                'salary'             => ['required', 'json', 'nullable'],
            ]
        );

        try {
            $vacancy = new Vacancy();
            $vacancy->fill($request->only(
                [
                    'title',
                    'active',
                    'announcement_text',
                    'detail_text',
                    'description',
                    'conditions',
                    'locations',
                    'language_level',
                    'grade',
                    'country',
                    'remote_format',
                    'technologies',
                    'specialisations',
                    'offer_timeline',
                    'vacancy_type',
                    'work_schedule',
                    'type_of_employment',
                    'work_experience',
                    'salary',
                ]
            )
            );
            $vacancy->created_by = $request->user()->id;
            $vacancy->updated_by = $request->user()->id;
            $vacancy->created_at = Carbon::now();
            $vacancy->updated_at = Carbon::now();
            $vacancy->save();

            return response()->noContent(201);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    /**
     * Show specific vacancy
     */
    public function show(int $id): Response|JsonResponse
    {
        $vacancy = Vacancy::find($id);

        return response()->json($vacancy);
    }

    /**
     * Update vacancy
     */
    public function update(int $id, Request $request): Response|JsonResponse
    {
        $vacancy = Vacancy::findOrFail($id);

        if ($vacancy instanceof Vacancy) {
            $this->authorize('update-vacancy', $vacancy);

            $request->validate(
                [
                    'title'              => ['max:100', 'unique:vacancies,title'],
                    'active'             => ['bool'],
                    'announcement_text'  => ['string'],
                    'detail_text'        => ['nullable', 'string'],
                    'description'        => ['string'],
                    'conditions'         => ['json'],
                    'locations'          => ['json', 'nullable'],
                    'language_level'     => ['nullable', 'json'],
                    'grade'              => ['nullable', 'json'],
                    'country'            => ['nullable', 'json'],
                    'remote_format'      => ['bool'],
                    'technologies'       => ['json'],
                    'specialisations'    => ['json'],
                    'offer_timeline'     => ['json', 'nullable'],
                    'vacancy_type'       => ['json'],
                    'work_schedule'      => ['json'],
                    'type_of_employment' => ['json'],
                    'work_experience'    => ['json'],
                    'salary'             => ['json', 'nullable'],
                ]
            );

            try {
                $vacancy->fill($request->only(
                    [
                        'title',
                        'detail_text',
                        'active',
                        'announcement_text',
                        'description',
                        'conditions',
                        'locations',
                        'language_level',
                        'grade',
                        'country',
                        'remote_format',
                        'technologies',
                        'specialisations',
                        'offer_timeline',
                        'vacancy_type',
                        'work_schedule',
                        'type_of_employment',
                        'work_experience',
                        'salary',
                    ]
                ));
                $vacancy->updated_by = (int)$request->user()->id;
                $vacancy->updated_at = Carbon::now();
                $vacancy->save();

                return response()->noContent();
            } catch (\Exception $e) {
                return response()->json(['error' => $e->getMessage()], 500);
            }
        }

        throw new NotFoundHttpException('Не найдена вакансия с переданным ID');
    }

    /**
     * Permanently delete vacancy
     */
    public function destroy(int $id): Response|JsonResponse
    {
        $vacancy = Vacancy::findOrFail($id);

        if ($vacancy instanceof Vacancy) {
            $this->authorize('permanently-delete-vacancy', $vacancy);

            try {
                $vacancy->forceDelete();

                return response()->noContent();
            } catch (\Exception $e) {
                return response()->json(['error' => $e->getMessage()], 500);
            }
        }

        throw new NotFoundHttpException('Не найдена вакансия с переданным ID');
    }

    /**
     * Restore vacancy throw change status
     */
    public function restore(int $id): Response|JsonResponse
    {
        $vacancy = Vacancy::withTrashed()->where('id', $id);

        if ($vacancy instanceof Vacancy) {
            $this->authorize('restore-vacancy', $vacancy);

            try {
                Vacancy::withTrashed()->where('id', $id)->restore();

                return response()->noContent();
            } catch (\Exception $e) {
                return response()->json(['error' => $e->getMessage()], 500);
            }
        }

        throw new NotFoundHttpException('Не найдена вакансия с переданным ID');
    }

    /**
     * Delete vacancy throw change status
     */
    public function delete(int $id): Response|JsonResponse
    {
        $vacancy = Vacancy::findOrFail($id);

        if ($vacancy instanceof Vacancy) {
            $this->authorize('delete-vacancy', $vacancy);

            try {
                $vacancy->delete();

                return response()->noContent();
            } catch (\Exception $e) {
                return response()->json(['error' => $e->getMessage()], 500);
            }
        }

        throw new NotFoundHttpException('Не найдена вакансия с переданным ID');
    }
}
