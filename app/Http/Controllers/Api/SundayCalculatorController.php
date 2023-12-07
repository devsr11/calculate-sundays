<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Carbon\Carbon; 
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Validator;

class SundayCalculatorController extends Controller
{

    /**
     * function to calculate no of sundays between two dates.
     *
     * @return \Illuminate\Http\Response
     */
    public function calculateSundays(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'start_date' => 'required|date',
            'end_date'   => 'required|date|after:start_date',
        ]);
     
        if ($validator->fails()) {                          // Validate dates
            return response()->json(['error' => $validator->errors()], 400);
        }
       
       $startDate = Carbon::parse($request->start_date);
       $endDate   = Carbon::parse($request->end_date);
   
      
        if ($startDate->gt($endDate)) {                         // Check if the start date is greater than the end date
            return response()->json(['error' => 'Start date cannot be greater than the end date.']);
        }

       
       $diffInYears = $startDate->diffInYears($endDate);        // Check if dates are at least two years apart but no more than five

       if ($diffInYears < 2 || $diffInYears > 5) {
           return response()->json(['error' => 'Dates must be at least two years apart but no more than five.']);
       }
   
      
       if ($startDate->dayOfWeek === Carbon::SUNDAY) {          // Check if the start date is not a Sunday
           return response()->json(['error' => 'Start date cannot be a Sunday.']);
       }
   
       
       $sundaysCount = 0;
       $currentDate  = $startDate->copy();

       while ($currentDate->lte($endDate)) {
           if ($currentDate->dayOfWeek === Carbon::SUNDAY && $currentDate->day < 28) {   // Calculate Sundays between the two dates (excluding Sundays after the 28th of the month)
               $sundaysCount++;
           }
           $currentDate->addDay();
       }
       return response()->json(["No of Sundays" => $sundaysCount]);
    }
}
