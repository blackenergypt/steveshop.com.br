<?php
/**
 * Funções gerais do sistema
 */

/**
 * Função para limpar strings e prevenir XSS
 *
 * @param string $str String a ser limpa
 * @return string String limpa
 */
function cleanStr($str) {
    return htmlspecialchars(trim($str), ENT_QUOTES, 'UTF-8');
}

/**
 * Função para gerar URL amigável (slug)
 *
 * @param string $str String a ser convertida em slug
 * @return string Slug gerado
 */
function slugify($str) {
    $str = mb_strtolower($str, 'UTF-8');
    $str = preg_replace('/[^a-z0-9-]/', '-', $str);
    $str = preg_replace('/-+/', '-', $str);
    return trim($str, '-');
}

/**
 * Função para formatar valor monetário
 *
 * @param float $value Valor a ser formatado
 * @param string $currency Símbolo da moeda
 * @return string Valor formatado
 */
function formatMoney($value, $currency = 'R$') {
    return $currency . ' ' . number_format($value, 2, ',', '.');
}

/**
 * Função para redirecionar
 *
 * @param string $url URL de destino
 * @return void
 */
function redirect($url) {
    header('Location: ' . $url);
    exit;
} 