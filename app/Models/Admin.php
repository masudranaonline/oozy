<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class Admin extends Authenticatable
{
    use HasFactory,HasApiTokens;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'phone',
        'password',
        'status',
        'role'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];
    public function isSuperAdmin()
    {
        // Adjust this condition according to how you define superadmin in your database
        return $this->role === 'superadmin'; // Assuming you have a 'role' column
    }
    // Technicians created by this user
    public function createdTechnicians(): MorphMany
    {
        return $this->morphMany(Technician::class, 'creator');
    }

    // Technicians updated by this user
    public function updatedTechnicians(): MorphMany
    {
        return $this->morphMany(Technician::class, 'updater');
    }
     // Operators created by this user
     public function createdOperators(): MorphMany
     {
         return $this->morphMany(Operator::class, 'creator');
     }

     // Operators updated by this user
     public function updatedOperators(): MorphMany
     {
         return $this->morphMany(Operator::class, 'updater');
     }
    // Brands created by this user
    public function createdBrands(): MorphMany
    {
        return $this->morphMany(Brand::class, 'creator');
    }

    // Brands updated by this user
    public function updatedBrands(): MorphMany
    {
        return $this->morphMany(Brand::class, 'updater');
    }

     // Models created by this user
     public function createdModels(): MorphMany
     {
         return $this->morphMany(ProductModel::class, 'creator');
     }

     // Models updated by this user
     public function updatedModels(): MorphMany
     {
         return $this->morphMany(ProductModel::class, 'updater');
     }


    // Category created by this user
    public function createdCategorys(): MorphMany
    {
        return $this->morphMany(Category::class, 'creator');
    }

    // Categorys updated by this user
    public function updatedCategorys(): MorphMany
    {
        return $this->morphMany(Category::class, 'updater');
    }
    // Units created by this user
    public function createdUnits(): MorphMany
    {
        return $this->morphMany(Unit::class, 'creator');
    }

    // Units updated by this user
    public function updatedUnits(): MorphMany
    {
        return $this->morphMany(Unit::class, 'updater');
    }
    // Parse Units created by this user
    public function createdParseUnits(): MorphMany
    {
        return $this->morphMany(Unit::class, 'creator');
    }
    // Parse Units updated by this user
    public function updatedParseUnits(): MorphMany
    {
        return $this->morphMany(Unit::class, 'updater');
    }
    // Lines created by this user
    public function createdLines(): MorphMany
    {
        return $this->morphMany(Line::class, 'creator');
    }

    // Lines updated by this user
    public function updatedLines(): MorphMany
    {
        return $this->morphMany(Line::class, 'updater');
    }

    // groups created by this user
    public function createdGroups(): MorphMany
    {
        return $this->morphMany(Group::class, 'creator');
    }

    // groups updated by this user
    public function updatedGroups(): MorphMany
    {
        return $this->morphMany(Group::class, 'updater');
    }
    // factorys created by this user
    public function createdFactorys(): MorphMany
    {
        return $this->morphMany(Factory::class, 'creator');
    }

    // factorys updated by this user
    public function updatedFactorys(): MorphMany
    {
        return $this->morphMany(Factory::class, 'updater');
    }

     // mechine assing created by this user
     public function createdMechineAssings(): MorphMany
     {
         return $this->morphMany(MechineAssing::class, 'creator');
     }
     //  mechine assing updated by this user
     public function updatedMechineAssings(): MorphMany
     {
         return $this->morphMany(MechineAssing::class, 'updater');
     }
    //Parse created by this user
    public function createdParses(): MorphMany
    {
        return $this->morphMany(Parse::class, 'creator');
    }
    //  mechine assing updated by this user
    public function updatedParses(): MorphMany
    {
        return $this->morphMany(Parse::class, 'updater');
    }
    public function createdParseInStocks(): MorphMany
    {
        return $this->morphMany(ParseStockIn::class, 'creator');
    }
    //  mechine assing updated by this user
    public function updatedParseInStocks(): MorphMany
    {
        return $this->morphMany(ParseStockIn::class, 'updater');
    }

    // mechine Stock created by this user
    public function createdMechineStocks(): MorphMany
    {
        return $this->morphMany(MechineStock::class, 'creator');
    }
    //  mechine Stock updated by this user
    public function updatedMechineStocks(): MorphMany
    {
        return $this->morphMany(MechineStock::class, 'updater');
    }

    // mechine Stock created by this user
    public function createdServices(): MorphMany
    {
        return $this->morphMany(Service::class, 'creator');
    }
    //  mechine Stock updated by this user
    public function updatedServices(): MorphMany
    {
        return $this->morphMany(Service::class, 'updater');
    }

     // mechine Stock created by this user
     public function createdServiceHistorys(): MorphMany
     {
         return $this->morphMany(ServiceHistory::class, 'creator');
     }
     //  mechine Stock updated by this user
     public function updatedServiceHistorys(): MorphMany
     {
         return $this->morphMany(ServiceHistory::class, 'updater');
     }

      // mechine Stock created by this user
    public function createdServiceParses(): MorphMany
    {
        return $this->morphMany(ServiceParse::class, 'creator');
    }
    //  mechine Stock updated by this user
    public function updatedServiceParses(): MorphMany
    {
        return $this->morphMany(ServiceParse::class, 'updater');
    }

    public function createdMovement(): MorphMany
    {
        return $this->morphMany(Movement::class, 'creator');
    }
    //  movement 
    public function updatedMovement(): MorphMany
    {
        return $this->morphMany(Movement::class, 'updater');
    }
}