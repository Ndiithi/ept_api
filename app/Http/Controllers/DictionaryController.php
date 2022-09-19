<?php

namespace App\Http\Controllers;

use App\Models\Dictionary;
use App\Services\SystemAuthorities;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Gate;

class DictionaryController extends Controller
{

    public function getAll(Request $request)
    {
        if (!Gate::allows(SystemAuthorities::$authorities['view_dictionary'])) {
            return response()->json(['Message' => 'Not allowed to view dictionary items: '], 500);
        }

        $data = Dictionary::paginate(request()->all());
        return Response::json($data, 200);
    }
    
    public function createEntry(Request $request)
    {
        if (!Gate::allows(SystemAuthorities::$authorities['add_dictionary'])) {
            return response()->json(['Message' => 'Not allowed to create dictionary item: '], 500);
        }
        try {

            $dictionary = new Dictionary([
                'name' => $request->name,
                'description' => $request->description,
                'meta' => $request->meta,
            ]);
            $dictionary->save();

            return response()->json(['Message' => 'Created successfully'], 200);
        } catch (Exception $ex) {

            return ['Error' => '500', 'Message' => 'Could not save dictionary item ' . $ex->getMessage()];
        }
    }
    
    public function deleteItem(Request $request)
    {
        if (!Gate::allows(SystemAuthorities::$authorities['delete_dictionary'])) {
            return response()->json(['Message' => 'Not allowed to delete item from dictionary: '], 500);
        }
        try {
            DB::delete('delete from dictionaries where uuid=?)', [$request->id]);
            return response()->json(['Message' => 'Deleted successfully'], 200);
        } catch (Exception $ex) {
            return response()->json(['Message' => 'Delete failed.  Error code' . $ex->getMessage()], 500);
        }
    }
    
    public function updateItem(Request $request)
    {
        if (!Gate::allows(SystemAuthorities::$authorities['edit_dictionary'])) {
            return response()->json(['Message' => 'Not allowed to edit dictionary item: '], 500);
        }
        try {

            $dictionary =  Dictionary::where('uuid', '=', $request->id)->first();
            $dictionary->name->$request->name;
            $dictionary->description->$request->description;
            $dictionary->meta->$request->meta;
            $dictionary->save();

            return response()->json(['Message' => 'Updated successfully'], 200);
        } catch (Exception $ex) {
            return response()->json(['Message' => 'Could not save dictionary item: '  . $ex->getMessage()], 500);
        }
    }
}
