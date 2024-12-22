<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use App\Models\Source;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;

class SourceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $page         = $request->input('page', 1);
        $itemsPerPage = $request->input('itemsPerPage', 5);
        $sortBy       = $request->input('sortBy', 'created_at'); // Default sort by created_at
        $sortOrder    = $request->input('sortOrder', 'desc');    // Default sort order is descending
        $search       = $request->input('search', '');           // Search term, default is empty
        // Determine the authenticated user (either from 'admin' or 'user' guard)
        if (Auth::guard('admin')->check()) {
            $currentUser = Auth::guard('admin')->user();
            $creatorType = Admin::class;
            // Check if the admin is a super admin
            if ($currentUser->role === 'superadmin') {
                // If superadmin, retrieve all technicians
                $sourcesQuery = Source::query(); // No filters applied
            } else {
                // If not superadmin, filter by creator type and id
                $sourcesQuery = Source::where('creator_type', $creatorType)
                    ->where('creator_id', $currentUser->id);
            }
        } elseif (Auth::guard('user')->check()) {
            $currentUser = Auth::guard('user')->user();
            $creatorType = User::class;
            // For regular users, filter by creator type and id
            $sourcesQuery = Source::where('creator_type', $creatorType)
                ->where('creator_id', $currentUser->id);
        } else {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }
        // Apply search if the search term is not empty
        if (!empty($search)) {
            $sourcesQuery->where('name', 'LIKE', '%' . $search . '%');
        }
        // Apply sorting
        $sourcesQuery->orderBy($sortBy, $sortOrder);
        // Paginate results
        $sources = $sourcesQuery->with('creator:id,name')->paginate($itemsPerPage);
        // Return the response as JSON
        return response()->json([
            'items' => $sources->items(), // Current page items
            'total' => $sources->total(), // Total number of records
        ]);
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
    public function store(Request $request)
    {
        $validatedData = $request->validate(Source::validationRules());
        // Determine the authenticated user (either from 'admin' or 'user' guard)
        if (Auth::guard('admin')->check()) {
             $creator = Auth::guard('admin')->user();
             // Check if the admin is a superadmin
             if ($creator->role === 'superadmin') {
                 // Superadmin can create technician without additional checks
             } else {
                 // Regular admin authorization check can be implemented here if needed
             }

         } elseif (Auth::guard('user')->check()) {
             $creator = Auth::guard('user')->user();
             // If you want users to have specific restrictions, implement checks here
         } else {
             return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
         }
         // Create the technician and associate it with the creator
         $source = new Source($validatedData);
         $source->creator()->associate($creator);  // Assign creator polymorphically
         $source->updater()->associate($creator);  // Associate the updater
         $source->save(); // Save the technician to the database
         // Return a success response
         return response()->json(['success' => true, 'message' => 'source created successfully.'], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Source $source)
    {
       // Determine the authenticated user (either from 'admin' or 'user' guard)
        if (Auth::guard('admin')->check()) {
            $currentUser = Auth::guard('admin')->user();
            $creatorType = Admin::class;
            // Check if the admin is a super admin
            if ($currentUser->role === 'superadmin') {
                // Super admins can edit any source
                return response()->json([
                    'success' => true,
                    'source' => $source
                ], Response::HTTP_OK);
            }
        } elseif (Auth::guard('user')->check()) {
            $currentUser = Auth::guard('user')->user();
            $creatorType = User::class;
        } else {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }
        // Check if the source belongs to the current user or admin
        if ($source->creator_type !== $creatorType || $source->creator_id !== $currentUser->id) {
            return response()->json(['success' => false, 'message' => 'Forbidden: You are not authorized to edit this source.'], 403);
        }
        // Return the source data if authorized
        return response()->json([
            'success' => true,
            'source'   => $source
        ], Response::HTTP_OK);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Source $source)
    {
        // Validate the incoming request data
         $validatedData = $request->validate(Source::validationRules());

         // Determine the authenticated user (either from 'admin' or 'user' guard)
         if (Auth::guard('admin')->check()) {
             $currentUser = Auth::guard('admin')->user();
             $creatorType = Admin::class;

             // Check if the admin is a superadmin
             if ($currentUser->role === 'superadmin') {
                 // Superadmin can update without additional checks
             } else {
                 // Regular admin authorization check
                 if ($source->creator_type !== $creatorType || $source->creator_id !== $currentUser->id) {
                     return response()->json(['success' => false, 'message' => 'Forbidden: You are not authorized to update this source.'], 403);
                 }
             }

         } elseif (Auth::guard('user')->check()) {
             $currentUser = Auth::guard('user')->user();
             $creatorType = User::class;

             // Regular user authorization check
             if ($source->creator_type !== $creatorType || $source->creator_id !== $currentUser->id) {
                 return response()->json(['success' => false, 'message' => 'Forbidden: You are not authorized to update this source.'], 403);
             }
         } else {
             return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
         }
         // Update the source's details
         $source->fill($validatedData);
         $source->updater()->associate($currentUser); // Associate the updater
         $source->save();

         // Return a success response
         return response()->json(['success' => true, 'message' => 'source updated successfully.', 'source' => $source], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Source $source)
    {
         if (Auth::guard('admin')->check()) {
            $currentUser = Auth::guard('admin')->user();
            // Check if the admin is a superadmin
            if ($currentUser->role === 'superadmin') {
                // Superadmin can delete any source without additional checks
            } else {
                $creatorType = Admin::class;
                // Regular admin authorization check
                if ($source->creator_type !== $creatorType || $source->creator_id !== $currentUser->id) {
                    return response()->json(['success' => false, 'message' => 'Forbidden: You are not authorized to delete this source.'], 403);
                }
            }

        } elseif (Auth::guard('user')->check()) {
            $currentUser = Auth::guard('user')->user();
            $creatorType = User::class;
            // Regular user authorization check
            if ($source->creator_type !== $creatorType || $source->creator_id !== $currentUser->id) {
                return response()->json(['success' => false, 'message' => 'Forbidden: You are not authorized to delete this source.'], 403);
            }
        } else {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        try {
            // Delete the supplier
            $source->delete();
            return response()->json([
                'success' => true,
                'message' => 'source deleted successfully.'
            ], Response::HTTP_OK);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error deleting source: ' . $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
    public function sourcestrashedcount()
    {
        // Get the count of soft-deleted Sources
        $trashedCount = Source::onlyTrashed()->count();

        return response()->json([
            'trashedCount' => $trashedCount
        ], Response::HTTP_OK);
    }
    public function sourcestrashed(Request $request)
    {
        // Determine the authenticated user (either from 'admin' or 'user' guard)
        if (Auth::guard('admin')->check()) {
            $currentUser = Auth::guard('admin')->user();
            $creatorType = Admin::class;

            // Superadmin check: Allow access to all soft-deleted technicians
            if ($currentUser->role === 'superadmin') {
                // Fetch all trashed technicians without additional checks
                $SourcesQuery = Source::onlyTrashed();
            } else {
                // Regular admin authorization check
                $SourcesQuery = Source::onlyTrashed()
                    ->where('creator_id', $currentUser->id)
                    ->where('creator_type', $creatorType); // Only fetch soft-deleted records created by this admin
            }

        } elseif (Auth::guard('user')->check()) {
            $currentUser = Auth::guard('user')->user();
            $creatorType = User::class;

            // Regular user authorization check
            $SourcesQuery = Source::onlyTrashed()
                ->where('creator_id', $currentUser->id)
                ->where('creator_type', $creatorType); // Only fetch soft-deleted records created by this user

        } else {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        // Get parameters from the request
        $page         = $request->input('page', 1);
        $itemsPerPage = $request->input('itemsPerPage', 5);
        $sortBy       = $request->input('sortBy', 'created_at'); // Default sort by created_at
        $sortOrder    = $request->input('sortOrder', 'desc'); // Default order is descending
        $search       = $request->input('search', ''); // Search term, default is empty

        // Apply search if the search term is not empty
        if (!empty($search)) {
            $SourcesQuery->where('name', 'LIKE', '%' . $search . '%'); // Adjust as per your Source fields
        }

        // Apply sorting
        $SourcesQuery->orderBy($sortBy, $sortOrder);

        // Paginate results
        $Sources = $SourcesQuery->paginate($itemsPerPage);

        // Return the response as JSON
        return response()->json([
            'items' => $Sources->items(), // Current page items
            'total' => $Sources->total(), // Total number of trashed records
        ]);

    }
    // Permanently delete a Source from trash
    public function sourcesforcedelete($id)
    {
        // Determine the authenticated user (either from 'admin' or 'user' guard)
        if (Auth::guard('admin')->check()) {
                
            $currentUser = Auth::guard('admin')->user();
            $creatorType = Admin::class;

            // Superadmin check: Allow access to all trashed technicians
            if ($currentUser->role === 'superadmin') {
                $source = Source::onlyTrashed()->findOrFail($id);
            } else {
                // Regular admin authorization check
                $source = Source::onlyTrashed()
                    ->where('creator_id', $currentUser->id)
                    ->where('creator_type', $creatorType)
                    ->findOrFail($id);
            }

            } elseif (Auth::guard('user')->check()) {
                $currentUser = Auth::guard('user')->user();
                $creatorType = User::class;

                // Regular user authorization check
                $source = Source::onlyTrashed()
                    ->where('creator_id', $currentUser->id)
                    ->where('creator_type', $creatorType)
                    ->findOrFail($id);
            } else {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }
        
        try {
            // Delete the supplier
            $source->forceDelete();
            return response()->json([
                'success' => true,
                'message' => 'Source permanently deleted successfully.'
            ], Response::HTTP_OK);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error deleting Source: ' . $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
       
    }

     // Restore a soft-deleted Source
    public function sourcesrestore($id)
    {
        
        // Determine the authenticated user (either from 'admin' or 'user' guard)
        if (Auth::guard('admin')->check()) {
            $currentUser = Auth::guard('admin')->user();
            $creatorType = Admin::class;

            // Superadmin check: Allow access to all trashed technicians
            if ($currentUser->role === 'superadmin') {
                $restored = Source::onlyTrashed()->findOrFail($id)->restore();
            } else {
                // Regular admin authorization check
                $restored = Source::onlyTrashed()
                    ->where('creator_id', $currentUser->id)
                    ->where('creator_type', $creatorType)
                    ->findOrFail($id)
                    ->restore();
            }

        } elseif (Auth::guard('user')->check()) {
            $currentUser = Auth::guard('user')->user();
            $creatorType = User::class;

            // Regular user authorization check
            $restored = Source::onlyTrashed()
                ->where('creator_id', $currentUser->id)
                ->where('creator_type', $creatorType)
                ->findOrFail($id)
                ->restore();
        } else {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }
        if ($restored) {
            return response()->json(['message' => 'Source restored successfully'], Response::HTTP_OK);
        }
        return response()->json(['message' => 'Source not found or is not trashed'], Response::HTTP_NOT_FOUND);
    }
}
