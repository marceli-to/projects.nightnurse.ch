<?php
namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use App\Http\Resources\DataCollection;
use App\Models\Company;
use App\Http\Requests\CompanyStoreRequest;
use Illuminate\Http\Request;

class CompanyController extends Controller
{
  /**
   * Get a list of companies
   * 
   * @return \Illuminate\Http\Response
   */
  public function get()
  {
    return new DataCollection(Company::orderBy('owner', 'DESC')->orderBy('name')->get());
  }

  /**
   * Get a single companies for a given companies
   * 
   * @param Company $company
   * @return \Illuminate\Http\Response
   */
  public function find(Company $company)
  {
    $company = Company::findOrFail($company->id);
    return response()->json($company);
  }

  /**
   * Store a newly created company
   *
   * @param  \Illuminate\Http\CompanyStoreRequest $request
   * @return \Illuminate\Http\Response
   */
  public function store(CompanyStoreRequest $request)
  {
    $data = $request->all();
    $data['uuid'] = \Str::uuid();
    $company = Company::create($data);
    return response()->json(['companyId' => $company->id]);
  }

  /**
   * Update a company for a given company
   *
   * @param Company $company
   * @param  \Illuminate\Http\CompanyStoreRequest $request
   * @return \Illuminate\Http\Response
   */
  public function update(Company $company, CompanyStoreRequest $request)
  {
    $company = Company::findOrFail($company->id);
    $company->update($request->all());
    $company->save();
    return response()->json('successfully updated');
  }

  /**
   * Toggle the status a given company
   *
   * @param  Company $company
   * @return \Illuminate\Http\Response
   */
  public function toggle(Company $company)
  {
    $company->publish = $company->publish == 0 ? 1 : 0;
    $company->save();
    return response()->json($company->publish);
  }

  /**
   * Remove a company
   *
   * @param  Company $company
   * @return \Illuminate\Http\Response
   */
  public function destroy(Company $company)
  {
    $company->delete();
    return response()->json('successfully deleted');
  }
}
