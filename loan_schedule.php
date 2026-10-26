<?php
// Loan-er kisti (installment) schedule ar jorimana-r hishab.
//
// Total interest = principal x interest_rate% x mash / 12   (simple interest)
// Total payable  = principal + total interest
// Total payable-ke soman kisti-te bhag kora hoy (mul taka ar interest alada alada bhag hoy).
//
// Kisti-r dhoron:
//   monthly       -> protimash e ekta (mash soman koyta)
//   half_monthly  -> 15 diner por por (mash x 2 ta)
//   weekly        -> 7 diner por por (mash x 52/12, nikotatom purno songkha)
//
// Status: paid / due (shomoy ache) / out_of_time (shomoy shesh, ekhono dey nai)
// Jorimana = kisti-r amount x penalty_rate% x deri-r din / 365  (faka hole interest rate-er soman)

function add_months_clamped($date, $months)
{
    $d = new DateTime($date);
    $day = (int) $d->format('j');
    $d->modify('first day of this month');
    $d->modify('+' . (int) $months . ' month');
    $last = (int) $d->format('t');
    $d->setDate((int) $d->format('Y'), (int) $d->format('n'), min($day, $last));
    return $d->format('Y-m-d');
}

function add_days_str($date, $days)
{
    $d = new DateTime($date);
    $d->modify('+' . (int) $days . ' day');
    return $d->format('Y-m-d');
}

function valid_repayment_type($type)
{
    return in_array($type, ['weekly', 'half_monthly', 'monthly'], true);
}

function installment_dates($loan_date, $months, $type)
{
    $dates = [];
    if ($type === 'monthly') {
        for ($k = 1; $k <= $months; $k++) {
            $dates[] = add_months_clamped($loan_date, $k);
        }
    } elseif ($type === 'half_monthly') {
        $n = $months * 2;
        for ($k = 1; $k <= $n; $k++) {
            if ($k % 2 === 0) {
                $dates[] = add_months_clamped($loan_date, $k / 2);
            } else {
                $dates[] = add_days_str(add_months_clamped($loan_date, ($k - 1) / 2), 15);
            }
        }
    } else {
        $n = max(1, (int) round($months * 52 / 12));
        for ($k = 1; $k <= $n; $k++) {
            $dates[] = add_days_str($loan_date, 7 * $k);
        }
    }
    return $dates;
}

function build_schedule($principal, $rate, $months, $type, $loan_date)
{
    $dates = installment_dates($loan_date, (int) $months, $type);
    $n = count($dates);
    $total_interest = round($principal * $rate / 100 * $months / 12, 2);
    $p_part = round($principal / $n, 2);
    $i_part = round($total_interest / $n, 2);

    $rows = [];
    for ($k = 0; $k < $n; $k++) {
        $last = ($k === $n - 1);
        $rows[] = array(
            'installment_no' => $k + 1,
            'due_date' => $dates[$k],
            // Shesh kisti-te bakita jug hoy, jate jog-fol thik thake
            'principal_part' => $last ? round($principal - $p_part * ($n - 1), 2) : $p_part,
            'interest_part' => $last ? round($total_interest - $i_part * ($n - 1), 2) : $i_part
        );
    }

    return array(
        'installments' => $rows,
        'total_interest' => $total_interest,
        'total_payable' => round($principal + $total_interest, 2),
        'due_date' => $dates[$n - 1]
    );
}

function effective_penalty_rate($loan)
{
    return ($loan->penalty_rate !== null && $loan->penalty_rate !== '') ? (float) $loan->penalty_rate : (float) $loan->interest_rate;
}

function days_late($due_date, $as_of)
{
    if ($as_of <= $due_date) {
        return 0;
    }
    return (int) (new DateTime($due_date))->diff(new DateTime($as_of))->days;
}

function installment_penalty($inst, $penalty_rate, $as_of)
{
    $days = days_late($inst->due_date, $as_of);
    if ($days <= 0 || $penalty_rate <= 0) {
        return 0.0;
    }
    return round(((float) $inst->principal_part + (float) $inst->interest_part) * $penalty_rate / 100 * $days / 365, 2);
}

function installment_status($inst, $as_of)
{
    if ($inst->status === 'paid') {
        return 'paid';
    }
    return $inst->due_date < $as_of ? 'out_of_time' : 'due';
}
