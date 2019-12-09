<?php

namespace App\Http\Controllers;

use App\Person;
use GraphAware\Bolt\Protocol\V1\Session as Neo4j;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;

class PeopleController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('people.index', [
            'people' => Person::all(),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('people.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        dd($request->all());
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Person  $person
     * @return \Illuminate\Http\Response
     */
    public function show(Person $person)
    {
        $results = app(Neo4j::class)
            ->run("MATCH (p:Person)-[]-(friend) WHERE p.eloquentId = \"{$person->id}\" RETURN p, collect(friend) as friends")
            ->firstRecord();

        $personKey = array_search('p', $results->keys());
        $friendKey = array_search('friends', $results->keys());

        $siblings = app(Neo4j::class)
            ->run("MATCH (p:Person)-[:CHILD_OF]-(parent)-[:CHILD_OF]-(sibling) WHERE p.eloquentId = \"{$person->id}\"
            RETURN collect(sibling) as siblings")
            ->firstRecord();

        return view('people.show', [
            'person' => $results->values()[$personKey],
            'relationships' => $results->values()[$friendKey],
            'siblings' => $siblings->values()[0],
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Person  $person
     * @return \Illuminate\Http\Response
     */
    public function edit(Person $person)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Person  $person
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Person $person)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Person  $person
     * @return \Illuminate\Http\Response
     */
    public function destroy(Person $person)
    {
        //
    }
}
