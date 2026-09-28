<?php

namespace App\Services;

class SearchSpellCorrector
{
    /**
     * Palabras vacías (stopwords) comunes en español a ignorar al extraer términos significativos.
     *
     * @var array<int, string>
     */
    protected static array $stopwords = [
        'de', 'la', 'el', 'en', 'y', 'a', 'los', 'del', 'las', 'por', 'un', 'para', 'con',
        'no', 'una', 'su', 'al', 'lo', 'como', 'más', 'pero', 'sus', 'le', 'ya', 'o', 'este',
        'sí', 'porque', 'esta', 'son', 'entre', 'está', 'cuando', 'muy', 'sin', 'sobre', 'será',
        'nos', 'también', 'me', 'hasta', 'hay', 'donde', 'quien', 'desde', 'todo', 'nosotros',
        'durante', 'todos', 'uno', 'les', 'ni', 'contra', 'otros', 'ese', 'eso', 'ante', 'ellos',
        'e', 'esto', 'mí', 'antes', 'algunos', 'qué', 'unos', 'yo', 'otro', 'otras', 'otra', 'él',
        'tanto', 'esa', 'estos', 'mucho', 'quienes', 'nada', 'muchos', 'cual', 'sea', 'poco',
        'ella', 'estar', 'haber', 'estas', 'estaba', 'estamos', 'algunas', 'algo', 'nosotras',
    ];

    /**
     * Analiza el término de búsqueda para extraer tokens significativos, grupos de variantes y sugerencias.
     *
     * @return array{
     *     has_corrections: bool,
     *     corrected_query: string,
     *     significant_tokens: array<int, string>,
     *     token_groups: array<int, array<int, string>>
     * }
     */
    public static function analyzeQuery(string $query): array
    {
        $query = trim($query);

        if ($query === '') {
            return [
                'has_corrections' => false,
                'corrected_query' => '',
                'significant_tokens' => [],
                'token_groups' => [],
            ];
        }

        preg_match_all('/[\p{L}\p{N}]+/u', mb_strtolower($query), $matches);
        $rawWords = $matches[0] ?? [];

        $significantTokens = [];
        $tokenGroups = [];

        foreach ($rawWords as $word) {
            if (mb_strlen($word) >= 2 && ! in_array($word, self::$stopwords, true)) {
                $significantTokens[] = $word;
                $variants = [$word];

                // Heurísticas simples de singular / plural en español
                if (str_ends_with($word, 'es') && mb_strlen($word) > 3) {
                    $variants[] = mb_substr($word, 0, -2);
                } elseif (str_ends_with($word, 's') && mb_strlen($word) > 2) {
                    $variants[] = mb_substr($word, 0, -1);
                } else {
                    $variants[] = $word.'s';
                    $variants[] = $word.'es';
                }

                $tokenGroups[] = array_values(array_unique($variants));
            }
        }

        return [
            'has_corrections' => false,
            'corrected_query' => '',
            'significant_tokens' => array_values(array_unique($significantTokens)),
            'token_groups' => $tokenGroups,
        ];
    }
}
