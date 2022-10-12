<?php

namespace App\Http\Controllers;

use App\Models\Dictionary;
use App\Services\SystemAuthorities;
use Exception;
use Faker\Provider\Uuid;
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
            return response()->json(['message' => 'Not allowed to view dictionary items: '], 500);
        }

        $data = Dictionary::paginate(request()->all());
        return Response::json($data, 200);
    }

    public function getItem(Request $request)
    {
        if (!Gate::allows(SystemAuthorities::$authorities['view_dictionary'])) {
            return response()->json(['message' => 'Not allowed to view dictionary entry: '], 500);
        }
        if ($request->uuid) {
            $entry = Dictionary::where('uuid', $request->uuid)->first();
        } else {
            $entry = Dictionary::find($request->id);
        }
        if($entry == null){
            return response()->json(['message' => 'Dictionary entry not found. '], 404);
        }
        return  $entry;
    }
    
    public function createEntry(Request $request)
    {
        if (!Gate::allows(SystemAuthorities::$authorities['add_dictionary'])) {
            return response()->json(['message' => 'Not allowed to create dictionary item: '], 500);
        }
        try {
            // validate
            $this->validate($request, [
                'name' => 'required|alpha_dash|unique:dictionary,name|max:255',
                'value' => 'required|json',
            ]);

            $dictionary = new Dictionary([
                'uuid' => Uuid::uuid(),
                'name' => $request->name,
                'value' => json_encode($request->value),
                'description' => $request->description,
                'meta' => $request->meta ?? json_decode('{}'),
            ]);
            $dictionary->save();

            return response()->json(['message' => 'Created successfully'], 200);
        } catch (Exception $ex) {

            return ['Error' => '500', 'message' => 'Could not save dictionary item ' . $ex->getMessage()];
        }
    }
    
    public function deleteItem(Request $request)
    {
        if (!Gate::allows(SystemAuthorities::$authorities['delete_dictionary'])) {
            return response()->json(['message' => 'Not allowed to delete item from dictionary: '], 500);
        }
        try {
            // DB::delete('delete from dictionaries where uuid=?)', [$request->id]);
            if ($request->uuid) {
                $entry = Dictionary::where('uuid', $request->uuid)->first();
            } else {
                $entry = Dictionary::find($request->id);
            }
            if($entry == null){
                return response()->json(['message' => 'Entry not found. '], 404);
            }else{
                $entry->delete();
                return response()->json(['message' => 'Deleted successfully'], 200);
            }
        } catch (Exception $ex) {
            return response()->json(['message' => 'Delete failed.  Error code' . $ex->getMessage()], 500);
        }
    }
    
    public function updateItem(Request $request)
    {
        if (!Gate::allows(SystemAuthorities::$authorities['edit_dictionary'])) {
            return response()->json(['message' => 'Not allowed to edit dictionary item: '], 500);
        }
        try {
            if ($request->uuid) {
                $dictionary = Dictionary::where('uuid', $request->uuid)->first();
            } else {
                $dictionary = Dictionary::find($request->id);
            }
            $dictionary->name = $request->name ?? $dictionary->name;
            $dictionary->description = $request->description ?? $dictionary->description;
            $dictionary->meta = $request->meta ?? $dictionary->meta;
            $dictionary->save();

            return response()->json(['message' => 'Updated successfully'], 200);
        } catch (Exception $ex) {
            return response()->json(['message' => 'Could not save dictionary item: '  . $ex->getMessage()], 500);
        }
    }
}
