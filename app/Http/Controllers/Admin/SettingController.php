<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SettingController extends Controller
{
    /**
     * Show the grade upload settings page
     */
    public function gradeUploadSettings()
    {
        abort_unless(Auth::check() && Auth::user()->isAdmin(), 403);

        $allowTeacherGradeUpload = Setting::get('allow_teacher_grade_upload', '1') === '1';

        return view('admin.settings.grade-upload', [
            'allowTeacherGradeUpload' => $allowTeacherGradeUpload,
        ]);
    }

    /**
     * Update grade upload settings
     */
    public function updateGradeUploadSettings(Request $request)
    {
        abort_unless(Auth::check() && Auth::user()->isAdmin(), 403);

        // Get the raw input value (may be string '0' or array ['0', '1'] or ['1', '0'])
        $inputValue = $request->input('allow_teacher_grade_upload', '0');
        
        // Determine if checkbox was checked
        // If array, check if '1' exists (checkbox was checked)
        // If string, check if it's '1' (though this shouldn't happen with our form setup)
        if (is_array($inputValue)) {
            $value = in_array('1', $inputValue, true) ? '1' : '0';
        } else {
            $value = ($inputValue === '1' || $inputValue === 1 || $inputValue === true) ? '1' : '0';
        }
        
        Setting::set('allow_teacher_grade_upload', $value);

        return redirect()->route('admin.settings.grade-upload')
            ->with('status', 'Grade upload settings updated successfully.');
    }
}
