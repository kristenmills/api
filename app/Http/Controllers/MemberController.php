<?php

namespace App\Http\Controllers;

use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Member;

class MemberController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index(Request $request)
    {
        $queryParameters = array_filter(
            $request->only(['first_name', 'last_name', 'username', 'committee'])
        );

        if (array_key_exists('committee', $queryParameters)) {
            $committeeId = $queryParameters['committee'];
            unset($queryParameters['committee']);

            $members = Member::whereHas(
                'committees',
                function ($query) use ($committeeId) {
                    $query->where('committee_id', $committeeId);
                }
            )->where($queryParameters)->get();
        } else {
            $members = Member::where($queryParameters)->get();
        }

        return response()->json($members);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  Request  $request
     * @return Response
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'first_name' => 'required',
            'last_name' => 'required',
            'username' => 'required|unique:members,username|regex:/[a-zA-Z]{2,3}\d{4}/',
        ]);

        $member = new Member();

        $member->first_name = $request->input('first_name');
        $member->last_name = $request->input('last_name');
        $member->username = $request->input('username');

        $member->save();

        return response()->json($member);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function show($id)
    {
        try {
            $result = Member::with('memberships')->findOrFail($id);

            return response()->json($result);
        } catch (ModelNotFoundException $e) {
            return new JsonResponse(
                ['error' => 'not found'], Response::HTTP_NOT_FOUND
            );
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  Request  $request
     * @param  int  $id
     * @return Response
     */
    public function update(Request $request, $id)
    {
        $member = Member::findOrFail($id);

        $member->first_name = $request->input('first_name', $member->first_name);
        $member->last_name = $request->input('last_name', $member->last_name);

        $member->save();

        return response()->json($member);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function destroy($id)
    {
        Member::destroy($id);
    }
}
