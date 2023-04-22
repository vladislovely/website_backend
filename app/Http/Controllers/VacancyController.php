<?php

namespace App\Http\Controllers;

use App\Models\Vacancy;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Lottery;

class VacancyController extends Controller
{
    /**
     * Get all vacancies
     */
    public function index(Request $request): JsonResponse
    {
        $this->authorize('view-vacancies', Vacancy::class);

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
            $vacancy                     = new Vacancy();
            $vacancy->title              = $request->post('title');
            $vacancy->detail_image       = $request->post('detail_image');
            $vacancy->created_by         = $request->user()->id;
            $vacancy->updated_by         = (int)$request->user()->id;
            $vacancy->active             = $request->post('active');
            $vacancy->announcement_text  = $request->post('announcement_text');
            $vacancy->detail_text        = $request->post('detail_text');
            $vacancy->description        = $request->post('description');
            $vacancy->conditions         = $request->post('conditions');
            $vacancy->locations          = $request->post('locations');
            $vacancy->language_level     = $request->post('language_level');
            $vacancy->grade              = $request->post('grade');
            $vacancy->country            = $request->post('country');
            $vacancy->remote_format      = $request->post('remote_format');
            $vacancy->technologies       = $request->post('technologies');
            $vacancy->specialisations    = $request->post('specialisations');
            $vacancy->offer_timeline     = $request->post('offer_timeline');
            $vacancy->vacancy_type       = $request->post('vacancy_type');
            $vacancy->work_schedule      = $request->post('work_schedule');
            $vacancy->type_of_employment = $request->post('type_of_employment');
            $vacancy->work_experience    = $request->post('work_experience');
            $vacancy->salary             = $request->post('salary');
            $vacancy->created_at         = Carbon::now();
            $vacancy->updated_at         = Carbon::now();
            $vacancy->save();

            return response()->noContent(201);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    /**
     * Show specific vacancy
     */
    public function show(int $id)
    {
        $this->authorize('view-vacancy', Vacancy::class);

        $vacancy = Vacancy::find($id);

        return response()->json($vacancy);
    }

    /**
     * Edit specific vacancy
     */
    public function edit(int $id, Request $request)
    {
        $this->authorize('update-vacancy', Vacancy::class);

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

        $vacancy = Vacancy::find($id);

        if ($vacancy instanceof Vacancy) {
            try {
                $vacancy->title              = $request->post('title');
                $vacancy->detail_image       = $request->post('detail_image');
                $vacancy->updated_by         = (int)$request->user()->id;
                $vacancy->active             = $request->post('active');
                $vacancy->announcement_text  = $request->post('announcement_text');
                $vacancy->detail_text        = $request->post('detail_text');
                $vacancy->description        = $request->post('description');
                $vacancy->conditions         = $request->post('conditions');
                $vacancy->locations          = $request->post('locations');
                $vacancy->language_level     = $request->post('language_level');
                $vacancy->grade              = $request->post('grade');
                $vacancy->country            = $request->post('country');
                $vacancy->remote_format      = $request->post('remote_format');
                $vacancy->technologies       = $request->post('technologies');
                $vacancy->specialisations    = $request->post('specialisations');
                $vacancy->offer_timeline     = $request->post('offer_timeline');
                $vacancy->vacancy_type       = $request->post('vacancy_type');
                $vacancy->work_schedule      = $request->post('work_schedule');
                $vacancy->type_of_employment = $request->post('type_of_employment');
                $vacancy->work_experience    = $request->post('work_experience');
                $vacancy->salary             = $request->post('salary');
                $vacancy->updated_at         = Carbon::now();
                $vacancy->save();

                return response()->noContent(200);
            } catch (\Exception $e) {
                return response()->json(['error' => $e->getMessage()], 500);
            }
        }
        return response()->json(['message' => 'vacancy is not found'], 404);
    }

    /**
     * Permanently delete vacancy
     */
    public function update(int $id, Request $request)
    {
        $this->authorize('update-vacancy', Vacancy::class);

        try {
            $resource = Vacancy::findOrFail($id);
            $resource->fill($request->only(['active']));
            $resource->save();

            return response()->noContent();
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Permanently delete vacancy
     */
    public function destroy(int $id)
    {
        $this->authorize('permanently-delete-vacancy', Vacancy::class);

        try {
            Vacancy::query()->where('id', $id)->forceDelete();

            return response()->noContent();
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Restore vacancy throw change status
     */
    public function restore(int $id)
    {
        $this->authorize('restore-vacancy', Vacancy::class);

        try {
            Vacancy::withTrashed()->where('id', $id)->restore();

            return response()->noContent();
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Delete vacancy throw change status
     */
    public function delete(int $id)
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
    }
}
