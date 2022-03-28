<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\User;
use Illuminate\Http\Request;

class PatientController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $patients = User::patients()->paginate(10);
        return view('patients.index', compact('patients'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('patients.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $rules = [
            'name' => 'required|min:3',
            'email' => 'required|email',
            'dni' => 'digits:8',
            'address' => 'min:5',
            'phone' => 'min:7'

        ];
        $this->validate($request, $rules);

        //asignación masiva
        User::create(
            $request->only('name', 'email', 'dni', 'address' ,'phone')
            + [
                'role' => 'patient', 
                'password' => bcrypt($request->input('password'))
            ]
        );

        $notification = 'El paciente se ha registrado exitosamente';
        return redirect('/patients')->with(compact('notification'));
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(User $patient)
    {
        return view('patients.edit', compact('patient'));
        
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $rules = [
            'name' => 'required|min:3',
            'email' => 'required|email',
            'dni' => 'digits:8',
            'address' => 'min:5',
            'phone' => 'min:7'

        ];
        $this->validate($request, $rules);

        $user = User::patients()->findOrFail($id);
        
        $data = $request->only('name', 'email', 'dni', 'address' ,'phone');
        $password = $request->input('password');
        if ($password)
            $data['password'] = bcrypt($password);

        $user->fill($data);
        $user->save(); //UPDATE

        $notification = 'La información del paciente se ha actualizado exitosamente';
        return redirect('/patients')->with(compact('notification'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(User $patient)
    {
        $patientName = $patient->name;
        $patient->delete();

        $notification = "El médico $patientName se ha eliminado correctamente";
        return redirect('/patients')->with(compact('notification'));
    }
}