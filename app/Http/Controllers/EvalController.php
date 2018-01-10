<?php
/**
 * EvalController
 * 
 * Bastien Nicoud
 * v1.0.0
 */


namespace App\Http\Controllers;

use Illuminate\Http\Request;

use CPNVEnvironment\Environment;

use App\Visit;
use App\Criteria;
use App\CriteriaValue;
use App\Evaluation;
use App\EvaluationSection;


/**
 * EvalController
 * 
 * Provides the methods to complete, submit and validate the datas indicated by the user in the evaluation grid.
 */
class EvalController extends Controller
{
    /**
     * index
     *
     * Display de evaluation grid section home (just for dev)
     * 
     * @return view evalGrid/grid
     */
    public function index()
    {
        // For dev, we display the evaluations of the visit 30
        $eval = Evaluation::where('visit_id', '=', 30)->get();

        return view('evalGrid/home')->with(['evaluations' => $eval]);
    }




    /**
     * newEval
     * 
     * This method register a new evaluation for the connected user (linked to the vist table)
     * 1. get the id of the visit
     * 2. Check if the visit exists
     * 2. Add a record in the evaluations table to create the evaluation
     * 
     * @param Request $request
     * 
     * @return redirect
     */
    public function newEval(Request $request)
    {
        // Reach the id of the vist witch add the evaluation
        $visit = $request->route('visit');

        // Check if this vist really exists (prevent pest users)
        if (!Visit::where('id', '=', $visit)->exists()) {

            // The visit not exist
            // Return the visit list with an error message
            return redirect('visits')->with('status', 'ID de la visite non valide ?');
            //return view('visits/visits')->with(['message' => 'ID de la visite non valide ?']);

        } else {

            // The visit exists
            // Check if the user is authored
            if (Environment::currentUser()->getLevel() < 1) {

                // Student, have no acces to this function
                // Return the visit list with an error message
                return redirect('visits')->with('status', "Vous n'avez pas acces a cette fonction.");
                //return view('visits/visits')->with(['message' => "Vous n'avez pas acces a cette fonction."]);

            } else {

                // The user is authored
                // Create the new evaluation
                $evaluation = new Evaluation;
                $evaluation->visit_id = $visit;
                $evaluation->editable = 1;
                // Save it
                $evaluation->save();

                // Redirect the user on the edit page of this new grid
                return redirect('evalgrid/edit')->with('status', "Grille d'evaluation correctement créée !");

            }
        }
    }



    /**
     * editEval
     * 
     * Return to the user the evaluation grid to be edit
     */
    public function editEval()
    {
        return view('evalGrid/grid');
    }




    /**
     * editCriteriaValue
     * 
     * Save the user evaluation value in the database
     */




    /**
     * getEval
     * 
     * Get the evaluation grid from the database
     *
     * @return view evalGrid/grid
     */
    public function getEval()
    {
        // Here we get all the evaluation grid

        // Return a colection with all the evaluation sections in an array with the criterias of each section
        $temp = EvaluationSection::with('criterias')->get();

        return $temp;
    }
}
