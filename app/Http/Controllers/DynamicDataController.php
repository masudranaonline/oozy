<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use App\Models\BreakDownProblemNote;
use App\Models\Factory;
use App\Models\Floor;
use App\Models\Group;
use App\Models\Line;
use App\Models\MachineStatus;
use App\Models\MechineAssing;
use App\Models\Parse;
use App\Models\ProductModel;
use App\Models\Unit;
use App\Models\User;
use Illuminate\Http\Request;

class DynamicDataController extends Controller
{
    public function getCompanies(Request $request){

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
    public function getCompanyWaysFactories(Request $request)
    {
      $search = $request->query('search', '');
      $limit = $request->query('limit', 5);
      $companyId = $request->query('company_id');

      // Query factories by company and name
      $factories = Factory::where('company_id', $companyId)
          ->where('name', 'like', '%' . $search . '%')
          ->limit($limit)
          ->get();

      return response()->json($factories);
    }

    public function getFactories(Request $request){

        // Get search term and limit from the request, with defaults
        $search = $request->query('search', '');
        $limit  = $request->query('limit', 5); // Default limit of 10
        // Query to search for factories by name with a limit
        $factories = Factory::with('user:id,name')->where('name', 'like', '%' . $search . '%')
                    //  ->limit($limit)
                     ->get();
        // Return the factories as JSON
        return response()->json($factories);
    }

    public function getFloors(Request $request){

        // Get search term and limit from the request, with defaults
        $search = $request->query('search', '');
        $limit  = $request->query('limit', 5); // Default limit of 10
        // Query to search for factories by name with a limit
        $factories = Floor::with(['factories.user:id,name'])->where('name', 'like', '%' . $search . '%')
                    //  ->limit($limit)
                     ->get();
        // Return the factories as JSON
        return response()->json($factories);
    }
    public function getUnits(Request $request){

        // Get search term and limit from the request, with defaults
        $search = $request->query('search', '');
        $limit  = $request->query('limit', 5); // Default limit of 10
        // Query to search for factories by name with a limit
        $factories = Unit::with(['floors.factories.user'])->where('name', 'like', '%' . $search . '%')
                    //  ->limit($limit)
                     ->get();
        // Return the factories as JSON
        return response()->json($factories);
    }
    public function getBrandAll(Request $request){

        // Get search term and limit from the request, with defaults
        $search = $request->query('search', '');
        $limit  = $request->query('limit', 5); // Default limit of 10
        // Query to search for brands by name with a limit
        $brands  = Brand::where('name', 'like', '%' . $search . '%')
                     ->limit($limit)
                     ->get();
        // Return the brands as JSON
        return response()->json($brands);
    }



    // public function getModels(Request $request)
    // {
    //     // Get search term, limit, and brand_id from the request
    //     $search = $request->query('search', '');
    //     $limit  = $request->query('limit', 5); // Default limit of 5
    //     $brandId = $request->query('brand_id');

    //     if (!$brandId) {
    //         return response()->json([], 400); // Return empty if no brand_id is provided
    //     }

    //     // Query to search for models by brand_id and name with a limit
    //     $models  = ProductModel::where('brand_id', $brandId)
    //                  ->where('name', 'like', '%' . $search . '%')
    //                  ->limit($limit)
    //                  ->get();

    //     // Return the models as JSON
    //     return response()->json($models);
    // }

    // public function getModels(Request $request)
    // {
    //     $search = $request->query('search', '');
    //     $limit = $request->query('limit', 5);

    //     $models = ProductModel::where('name', 'like', '%' . $search . '%')
    //         ->limit($limit)
    //         ->get();
    //     return response()->json($models);
    // }
    public function getModels(Request $request)
    {
      $search   = $request->query('search', '');
      $limit    = $request->query('limit', 5); // Default limit of 5
      $brand_id = $request->query('brand_id');

      $query = ProductModel::query();

      if ($brand_id) {
          $query->where('brand_id', $brand_id); // Filter by brand_id
      }

      $models = $query->where('name', 'like', '%' . $search . '%')
                      ->limit($limit)
                      ->get();

      return response()->json($models);
    }

    /**
     * Fetch brands based on the selected model.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getBrands(Request $request)
    {
      // Get search term and limit from the request, with defaults
      $search = $request->query('search', '');
      $limit = $request->query('limit', 5); // Default limit of 5

      // Query to search for brands by name with a limit
      $brands = Brand::where('name', 'like', '%' . $search . '%')
          ->limit($limit)
          ->get();

      // Return the brands as JSON
      return response()->json($brands);
  }
  public function getGroups(Request $request)
    {
      // Get search term and limit from the request, with defaults
      $search = $request->query('search', '');
      $limit = $request->query('limit', 5); // Default limit of 5

      // Query to search for brands by name with a limit
      $groups = Group::where('name', 'like', '%' . $search . '%')
          ->limit($limit)
          ->get();

      // Return the groups as JSON
      return response()->json($groups);
  }
  public function getParts(Request $request)
  {
      // Get search term and limit from the request, with defaults
      $search = $request->query('search', '');
      $limit = $request->query('limit', 5); // Default limit of 5

      // Query to search for brands by name with a limit
      $parts = Parse::where('name', 'like', '%' . $search . '%')
          ->limit($limit)
          ->get();

      // Return the parts as JSON
      return response()->json($parts);
  }

    // public function getBrands(Request $request)
    // {
    //     $modelId = $request->query('model_id');
    //     $search = $request->query('search', '');
    //     $limit = $request->query('limit', 5);

    //     if (!$modelId) {
    //         return response()->json([]);
    //     }

    //     $model = ProductModel::find($modelId);

    //     if (!$model) {
    //         return response()->json([]);
    //     }

    //     $query = Brand::where('id', $model->brand_id);

    //     if ($search) {
    //         $query->where('name', 'like', '%' . $search . '%');
    //     }

    //     $brands = $query->limit($limit)->get();

    //     return response()->json($brands);
    // }

    public function getMachineStatus(Request $request){

        // Get search term and limit from the request, with defaults
        $search = $request->query('search', '');
        $limit  = $request->query('limit', 20); // Default limit of 10
        // Query to search for brands by name with a limit
        $machineStatus  = MachineStatus::where('name', 'like', '%' . $search . '%')
                    //  ->limit($limit)
                     ->get();
        // Return the machineStatus as JSON
        return response()->json($machineStatus);
    }
    public function getLinesByMachine(Request $request)
    {
        $machineId = $request->query('machine_id');
        if (!$machineId) {
            return response()->json(['error' => 'Machine ID is required'], 400);
        }

        // Fetch lines associated with the factory of the selected machine
        $lines = Line::whereHas('units.floors.factories', function ($query) use ($machineId) {
            $query->where('id', MechineAssing::find($machineId)->factory_id);
        })->get(['id', 'name']);

        return response()->json($lines);
    }
    public function getLinesByFactory(Request $request)
    {
        $factoryId = $request->input('factory_id');
        // dd( $factoryId);

        // Fetch lines through the relationship chain
        $lines = Line::whereHas('units.floors.factories', function ($query) use ($factoryId) {
            $query->where('id', $factoryId);
        })
        ->select('id', 'name') // Select only necessary columns
        ->get();

        return response()->json($lines);
    }
    public function getMachineCodes(Request $request)
    {
        // Get search term and limit from the request, with defaults
        $search      = $request->query('search', '');
        $limit       = $request->query('limit', 5); // Default limit of 10
        // Query to search for machines by code with a limit
        $machineCodes = MechineAssing::selectRaw('
                            MAX(id) as id,
                            machine_code,
                            MAX(location_status) as location_status,
                            MAX(created_at) as created_at
                        ')
                        ->where('machine_code', 'like', '%' . $search . '%')
                        ->where('location_status', 'Sewing Line')
                        ->groupBy('machine_code') // Group by machine_code to ensure uniqueness
                        ->limit($limit)
                        ->get();
        // Return the machineCodes as JSON
        return response()->json($machineCodes);
    }
    public function getMachineLines(Request $request)
    {
        $machineId = $request->get('machine_id');
        // Validate machine_id
        if (!$machineId) {
            return response()->json(['error' => 'Machine ID is required.'], 400);
        }
        // Fetch assigned line IDs from MachineAssign
        $assignedLineIds = MechineAssing::where('id', $machineId)->pluck('line_id');
        // Fetch the lines corresponding to these IDs
        $lines = Line::whereIn('id', $assignedLineIds)->get();

        return response()->json($lines);
    }

    public function getBreakdownProblemNotes(Request $request)
    {
        $search = $request->query('search', '');
        $limit = $request->query('limit', 5); // Default limit of 5

        // Query to search for brands by name with a limit
        $breakDownProblemNotes = BreakDownProblemNote::where('break_down_problem_note', 'like', '%' . $search . '%')
            ->limit($limit)
            ->get();

        // Return the BreakDownProblemNotes as JSON
        return response()->json($breakDownProblemNotes);
    }

}