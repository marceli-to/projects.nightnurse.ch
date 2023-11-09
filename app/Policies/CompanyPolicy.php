<?php
namespace App\Policies;
use App\Models\User;
use App\Models\Company;
use Illuminate\Auth\Access\HandlesAuthorization;

class CompanyPolicy
{
  use HandlesAuthorization;

  /**
   * Determine whether the user can view the company.
   *
   * @param  \App\Models\User  $user
   * @param  \App\Models\Company  $company
   * @return bool
   */
  public function view(User $user, Company $company)
  {
    return $user->company_id === $company->id || $user->isAdmin();
  }
}
