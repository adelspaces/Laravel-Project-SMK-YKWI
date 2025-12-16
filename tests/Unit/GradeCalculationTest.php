<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\User;
use App\Models\GradeConfiguration;
use App\Services\GradeCalculationService;
use Illuminate\Foundation\Testing\RefreshDatabase;

class GradeCalculationTest extends TestCase
{
    use RefreshDatabase;

    protected $gradeCalculationService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->gradeCalculationService = new GradeCalculationService();
        
        // Create grade configuration
        GradeConfiguration::create([
            'attendance_weight' => 50,
            'assessment_weight' => 50,
            'grade_thresholds' => [
                "A" => 80,
                "A-" => 75,
                "B+" => 70,
                "B" => 60,
                "B-" => 55,
                "C+" => 50,
                "C" => 40,
                "C-" => 35,
                "D" => 30,
                "E" => 0
            ]
        ]);
    }

    /** @test */
    public function it_assigns_letter_grades_correctly()
    {
        // Test the letter grade assignment logic directly
        $config = GradeConfiguration::first();
        
        // Test A grade
        $grade = $this->invokeMethod($this->gradeCalculationService, 'assignLetterGrade', [90, $config->grade_thresholds]);
        $this->assertEquals('A', $grade);
        
        // Test B grade
        $grade = $this->invokeMethod($this->gradeCalculationService, 'assignLetterGrade', [65, $config->grade_thresholds]);
        $this->assertEquals('B', $grade);
        
        // Test C- grade
        $grade = $this->invokeMethod($this->gradeCalculationService, 'assignLetterGrade', [35, $config->grade_thresholds]);
        $this->assertEquals('C-', $grade);
        
        // Test D grade
        $grade = $this->invokeMethod($this->gradeCalculationService, 'assignLetterGrade', [32, $config->grade_thresholds]);
        $this->assertEquals('D', $grade);
        
        // Test E grade
        $grade = $this->invokeMethod($this->gradeCalculationService, 'assignLetterGrade', [25, $config->grade_thresholds]);
        $this->assertEquals('E', $grade);
    }
    
    /**
     * Call protected/private method of a class.
     *
     * @param object &$object    Instantiated object that we will run method on.
     * @param string $methodName Method name to call
     * @param array  $parameters Array of parameters to pass into method.
     *
     * @return mixed Method return.
     */
    protected function invokeMethod(&$object, $methodName, array $parameters = array())
    {
        $reflection = new \ReflectionClass(get_class($object));
        $method = $reflection->getMethod($methodName);
        $method->setAccessible(true);

        return $method->invokeArgs($object, $parameters);
    }
}