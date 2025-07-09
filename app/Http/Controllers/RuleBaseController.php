<?php

namespace App\Http\Controllers;

use App\Models\RuleBase;
use App\Models\Notification;
use Illuminate\Http\Request;

class RuleBaseController extends Controller
{
    public function index()
    {
        try {
            //$rules = RuleBase::all();
            return view('rule_base',[
                'notifications' => match(auth()->user()->role_id) {
                    1 => collect([]),
                    2, 3 => Notification::where('clinic_id', auth()->user()->clinic_id)->where('is_read', 0)->get(),
                    default => Notification::where('user_id', auth()->id())->where('is_read', 0)->get(),
                },
                'questions' => RuleBase::all()
            ]);
            
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to fetch rules: ' . $e->getMessage());
        }
    }

    public function upsert(Request $request,$id = null)
    {
        try {
            //dd($request->all());
            $validatedData = $request->validate([
                'id' => 'nullable|integer',
                'target' => 'required|string',
                'question' => 'required|string|max:255',
                'yesValue' => 'required|string',
                'noValue' => 'required|string'
            ]);

            $rule = $id === null ? new RuleBase() : RuleBase::findOrFail($id);
            $rule->fill([
                    'target' => $validatedData['target'],
                    'question' => $validatedData['question'],
                    'yes' => $validatedData['yesValue'],
                    'no' => $validatedData['noValue']
                ]);
            $rule->save();

            return redirect()->route('rule-base')->with('success', 
                $id ? 'Rule updated successfully' : 'Rule created successfully'
            );

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to save rule: ' . $e->getMessage()
            ], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $rule = RuleBase::findOrFail($id);
            $rule->delete();

            return response()->json([
                'success' => true,
                'message' => 'Rule deleted successfully'
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to delete rule: ' . $e->getMessage()
            ], 500);
        }
    }
}
