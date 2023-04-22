<?php

namespace App\Http\Controllers;

use App\Models\Vacancy;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class VacancyController extends Controller
{
    /**
     * Get all vacancies
     */
    public function index(): JsonResponse
    {
        $this->authorize('view-vacancies', Vacancy::class);

        return response()->json(Vacancy::all()->toArray());
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
            $vacancy->status             = 'STATUS_ACTIVE';
            $vacancy->created_at         = Carbon::now();
            $vacancy->updated_at         = Carbon::now();
            $vacancy->save();

            return response()->noContent(201);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Show specific vacancy
     */
    public function show(Vacancy $vacancy)
    {
        $this->authorize('view-vacancy', Vacancy::class);
    }

    /**
     * Edit specific vacancy
     */
    public function edit(Vacancy $vacancy)
    {
        $this->authorize('update-vacancy', Vacancy::class);
    }

    /**
     * Update the specified vacancy
     */
    public function update(Request $request, Vacancy $vacancy)
    {
        $this->authorize('update-vacancy', Vacancy::class);
    }

    /**
     * Permanently delete vacancy
     */
    public function destroy(Vacancy $vacancy)
    {
        $this->authorize('permanently-delete-vacancy', Vacancy::class);
    }

    /**
     * Restore vacancy throw change status
     */
    public function restore(Vacancy $vacancy)
    {
        $this->authorize('restore-vacancy', Vacancy::class);
    }

    /**
     * Delete vacancy throw change status
     */
    public function delete(Vacancy $vacancy)
    {
        $this->authorize('delete-vacancy', Vacancy::class);
    }
}
