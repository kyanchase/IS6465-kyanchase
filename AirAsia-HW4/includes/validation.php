<?php

function giftCardTypes(): array
{
    return ['Dining', 'Retail', 'Travel'];
}

function validateGiftCard(array $input): array
{
    $fields = [];
    foreach (['cardName', 'cardType', 'cardValue', 'points'] as $field) {
        $value = $input[$field] ?? '';
        $fields[$field] = is_string($value) ? trim($value) : '';
    }

    $errors = [];

    if ($fields['cardName'] === '' || mb_strlen($fields['cardName'], 'UTF-8') > 100) {
        $errors['cardName'] = 'Enter a card name of 1 to 100 characters.';
    }

    if (!in_array($fields['cardType'], giftCardTypes(), true)) {
        $errors['cardType'] = 'Choose Dining, Retail, or Travel.';
    }

    $cardValue = filter_var($fields['cardValue'], FILTER_VALIDATE_FLOAT);
    if ($cardValue === false || !is_finite($cardValue) || $cardValue <= 0) {
        $errors['cardValue'] = 'Enter a card value greater than zero.';
    }

    $points = filter_var($fields['points'], FILTER_VALIDATE_INT, [
        'options' => ['min_range' => 1],
    ]);
    if ($points === false) {
        $errors['points'] = 'Enter a positive whole number for points.';
    }

    return ['fields' => $fields, 'errors' => $errors];
}
