@extends('layouts.app')

@section('content')
    <h1>Welcome {{ Auth::user()->full_name }}</h1>
