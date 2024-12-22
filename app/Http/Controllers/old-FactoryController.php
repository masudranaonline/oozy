<?php

namespace App\Http\Controllers;

use App\Models\Factory;
use App\Models\Floor;
use App\Models\Line;
use App\Models\Unit;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB; // Import the DB facade
use Illuminate\Support\Facades\Validator;

class FactoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    // public function store(Request $request)
    // {
    //     $factory = $request->all();

    //     return response()->json([
    //         'success' => true,
    //         'message' => 'factory updated successfully.',
    //         'factory' => $factory
    //     ]);

    // }
    public function store(Request $request)
    {
        // Validate the incoming request data
        $data = $request->validate([
            'company_id' => 'required', // Expect an for JSON storage
            'name' => 'required|string|max:255',
            'factory_code' => 'required|string|max:50|unique:factories,factory_code',
            'email' => 'nullable|email',
            'phone' => 'nullable|string|max:15',
            'location' => 'nullable|string|max:255',
            'status' => 'required|string|in:Active,Inactive',
            'floor_ids' => 'required',
            'floor_ids.*' => 'exists:floors,id',
            'unit_ids' => 'required',
            'unit_ids.*' => 'exists:units,id',
            'line_ids' => 'required',
            'line_ids.*' => 'exists:lines,id',
        ]);
   
        // Determine the authenticated user (either from 'admin' or 'user' guard)
        if (Auth::guard('admin')->check()) {
            $creator = Auth::guard('admin')->user();
            // Additional checks can be implemented here for admin roles if needed
        } elseif (Auth::guard('user')->check()) {
            $creator = Auth::guard('user')->user();
            // User-specific checks can be added here if needed
        } else {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }
    
        try {
            \DB::beginTransaction();
    
            // Create the factory
            $factory = new Factory([
                'company_id'   => json_encode($data['company_id']), // Convert array to JSON
                'name'         => $data['name'],
                'factory_code' => $data['factory_code'],
                'email' => $data['email'],
                'phone' => $data['phone'],
                'location' => $data['location'],
                'status' => $data['status'],
            ]);
            
            // Associate creator and updater polymorphically
            $factory->creator()->associate($creator);
            $factory->updater()->associate($creator);
            $factory->save(); // Save the factory to the database
            
                // Insert entries into the factory_floor pivot table
      

        // Attach units to each floor
        // foreach ($data['floor_ids'] as $floorId) {
        //     foreach ($data['unit_ids'] as $unitId) {
        //         \DB::table('floor_unit')->insert([ // Assuming you have a pivot table called 'floor_unit'
        //             'floor_id' => $floorId,
        //             'unit_id' => $unitId,
        //         ]);

        //         // Attach lines to each unit
        //         foreach ($data['line_ids'] as $lineId) {
        //             \DB::table('unit_line')->insert([ // Assuming you have a pivot table called 'unit_line'
        //                 'unit_id' => $unitId,
        //                 'line_id' => $lineId,
        //             ]);
        //         }
        //     }
        // }




            // if ($factory) {
            //     $factoryId = Factory::where('uuid',$factory->uuid)->first();
            //     $
            // }

            
    
            // // Sync floors with the factory
            // $factory->floors()->sync($data['floor_ids']);
    
            // // Attach units to each floor
            // foreach ($data['floor_ids'] as $floorId) {
            //     $floor = Floor::find($floorId);
            //     // Attach units to the floor
            //     $floor->units()->sync($data['unit_ids']);
                
            //     // Attach lines to each unit
            //     foreach ($data['unit_ids'] as $unitId) {
            //         $unit = Unit::find($unitId);
            //         // Attach lines to the unit
            //         $unit->lines()->sync($data['line_ids']);
            //     }
            // }
    
            \DB::commit();
    
            // Return a success response
            return response()->json(['success' => true, 'message' => 'Factory created successfully.']);
        } catch (\Exception $e) {
            \DB::rollBack();
            return response()->json(['success' => false, 'message' => 'Failed to create factory', 'error' => $e->getMessage()], 500);
        }
    }
    


    /**
     * Display the specified resource.
     */
    public function show(Factory $factory)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Factory $factory)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Factory $factory)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Factory $factory)
    {
        //
    }

    public function getCompanys(Request $request){

        // Get search term and limit from the request, with defaults
        $search = $request->query('search', '');
        $limit  = $request->query('limit', 5); // Default limit of 10
        // Query to search for users by name with a limit
        $users = User::where('name', 'like', '%' . $search . '%')
                     ->limit($limit)
                     ->get();
        // Return the users as JSON
        return response()->json($users);
    }
    public function getFloors(Request $request){

        // Get search term and limit from the request, with defaults
        $search = $request->query('search', '');
        $limit  = $request->query('limit', 5); // Default limit of 10
        // Query to search for floors by name with a limit
        $floors = Floor::where('name', 'like', '%' . $search . '%')
                     ->limit($limit)
                     ->get();
        // Return the floors as JSON
        return response()->json($floors);
    }
    public function getUnits(Request $request){

        // Get search term and limit from the request, with defaults
        $search = $request->query('search', '');
        $limit  = $request->query('limit', 5); // Default limit of 10
        // Query to search for units by name with a limit
        $units  = Unit::where('name', 'like', '%' . $search . '%')
                     ->limit($limit)
                     ->get();
        // Return the units as JSON
        return response()->json($units);
    }
    public function getLines(Request $request){

        // Get search term and limit from the request, with defaults
        $search = $request->query('search', '');
        $limit  = $request->query('limit', 5); // Default limit of 10
        // Query to search for lines by name with a limit
        $lines  = Line::where('name', 'like', '%' . $search . '%')
                     ->limit($limit)
                     ->get();
        // Return the lines as JSON
        return response()->json($lines);
    }

    private function generateCustomUuid()
    {
        // You can customize the UUID format here
        // Here is a simple example of generating a random UUID-like string
        $data = random_bytes(16);
        // Set the version (4) and variant bits
        $data[6] = chr(ord($data[6]) & 0x0f | 0x40); // Version 4
        $data[8] = chr(ord($data[8]) & 0x3f | 0x80); // Variant

        return vsprintf('%s-%s-%s-%s-%s', str_split(bin2hex($data), 4));
    }
}
