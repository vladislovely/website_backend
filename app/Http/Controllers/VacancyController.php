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
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {
        return response()->json(Vacancy::all()->toArray());
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): Response|JsonResponse
    {
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
     * Display the specified resource.
     */
    public function show(Vacancy $vacancy)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Vacancy $vacancy)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Vacancy $vacancy)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Vacancy $vacancy)
    {
        //
    }
}
