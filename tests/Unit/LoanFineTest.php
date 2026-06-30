<?php

namespace Tests\Unit;

use App\Models\Loan;
use Carbon\Carbon;
use Tests\TestCase;

class LoanFineTest extends TestCase
{
    public function test_late_loan_calculates_fine_from_due_date(): void
    {
        // 1 day late
        $loan1 = new Loan([
            'status' => 'dipinjam',
            'due_date' => Carbon::now()->subDay(),
        ]);
        $this->assertTrue($loan1->isLate());
        $this->assertSame(1000, $loan1->calculateFine());

        // 2 days late
        $loan2 = new Loan([
            'status' => 'dipinjam',
            'due_date' => Carbon::now()->subDays(2),
        ]);
        $this->assertTrue($loan2->isLate());
        $this->assertSame(2000, $loan2->calculateFine());

        // 3 days late
        $loan3 = new Loan([
            'status' => 'dipinjam',
            'due_date' => Carbon::now()->subDays(3),
        ]);
        $this->assertTrue($loan3->isLate());
        $this->assertSame(3000, $loan3->calculateFine());

        // 5 days late
        $loan5 = new Loan([
            'status' => 'dipinjam',
            'due_date' => Carbon::now()->subDays(5),
        ]);
        $this->assertTrue($loan5->isLate());
        $this->assertSame(5000, $loan5->calculateFine());
    }
}
