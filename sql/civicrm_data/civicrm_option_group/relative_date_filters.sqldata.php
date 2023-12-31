<?php
return CRM_Core_CodeGen_OptionGroup::create('relative_date_filters', 'a/0078')
  ->addMetadata([
    'title' => ts('Relative Date Filters'),
  ])
  ->addValueTable(['label', 'name'], [
    [ts('Today'), 'this.day'],
    [ts('This week'), 'this.week'],
    [ts('This calendar month'), 'this.month'],
    [ts('This quarter'), 'this.quarter'],
    [ts('This fiscal year'), 'this.fiscal_year'],
    [ts('This calendar year'), 'this.year'],
    [ts('Yesterday'), 'previous.day'],
    [ts('Previous week'), 'previous.week'],
    [ts('Previous calendar month'), 'previous.month'],
    [ts('Previous quarter'), 'previous.quarter'],
    [ts('Previous fiscal year'), 'previous.fiscal_year'],
    [ts('Previous calendar year'), 'previous.year'],
    [ts('Last 7 days including today'), 'ending.week'],
    [ts('Last 30 days including today'), 'ending.month', 'value' => 'ending_30.day'],
    [ts('Last 60 days including today'), 'ending_2.month', 'value' => 'ending_60.day'],
    [ts('Last 90 days including today'), 'ending.quarter', 'value' => 'ending_90.day'],
    // Suspicious... name and value disagree... ^^^
    [ts('Last 12 months including today'), 'ending.year'],
    [ts('Last 2 years including today'), 'ending_2.year'],
    [ts('Last 3 years including today'), 'ending_3.year'],
    [ts('Tomorrow'), 'starting.day'],
    [ts('Next week'), 'next.week'],
    [ts('Next calendar month'), 'next.month'],
    [ts('Next quarter'), 'next.quarter'],
    [ts('Next fiscal year'), 'next.fiscal_year'],
    [ts('Next calendar year'), 'next.year'],
    [ts('Next 7 days including today'), 'starting.week'],
    [ts('Next 30 days including today'), 'starting.month'],
    [ts('Next 60 days including today'), 'starting_2.month'],
    [ts('Next 90 days including today'), 'starting.quarter'],
    [ts('Next 12 months including today'), 'starting.year'],
    [ts('Current week to-date'), 'current.week'],
    [ts('Current calendar month to-date'), 'current.month'],
    [ts('Current quarter to-date'), 'current.quarter'],
    [ts('Current calendar year to-date'), 'current.year'],
    [ts('To end of yesterday'), 'earlier.day'],
    [ts('To end of previous week'), 'earlier.week'],
    [ts('To end of previous calendar month'), 'earlier.month'],
    [ts('To end of previous quarter'), 'earlier.quarter'],
    [ts('To end of previous calendar year'), 'earlier.year'],
    [ts('From start of current day'), 'greater.day'],
    [ts('From start of current week'), 'greater.week'],
    [ts('From start of current calendar month'), 'greater.month'],
    [ts('From start of current quarter'), 'greater.quarter'],
    [ts('From start of current calendar year'), 'greater.year'],
    [ts('To end of current week'), 'less.week'],
    [ts('To end of current calendar month'), 'less.month'],
    [ts('To end of current quarter'), 'less.quarter'],
    [ts('To end of current calendar year'), 'less.year'],
    [ts('Previous 2 days'), 'previous_2.day'],
    [ts('Previous 2 weeks'), 'previous_2.week'],
    [ts('Previous 2 calendar months'), 'previous_2.month'],
    [ts('Previous 2 quarters'), 'previous_2.quarter'],
    [ts('Previous 2 calendar years'), 'previous_2.year'],
    [ts('Previous 2 fiscal years'), 'previous_2.fiscal_year'],
    [ts('Day prior to yesterday'), 'previous_before.day'],
    [ts('Week prior to previous week'), 'previous_before.week'],
    [ts('Month prior to previous calendar month'), 'previous_before.month', 'is_default' => NULL],
    [ts('Quarter prior to previous quarter'), 'previous_before.quarter'],
    [ts('Year prior to previous calendar year'), 'previous_before.year'],
    [ts('Fiscal year prior to previous fiscal year'), 'previous_before.fiscal_year'],
    [ts('From end of previous week'), 'greater_previous.week'],
    [ts('From end of previous calendar month'), 'greater_previous.month'],
    [ts('From end of previous quarter'), 'greater_previous.quarter'],
    [ts('From end of previous calendar year'), 'greater_previous.year'],
  ])
  ->syncColumns('fill', ['name' => 'value'])
  ->addDefaults([
    'filter' => NULL,
  ]);
