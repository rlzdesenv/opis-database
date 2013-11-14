<?php
/* ===========================================================================
 * Opis Project
 * http://opis.io
 * ===========================================================================
 * Copyright 2013 Marius Sarca
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *    http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 * ============================================================================ */

namespace Opis\Database\Compiler;

use Opis\Database\SQL\Compiler;
use Opis\Database\SQL\Query;

class Oracle extends Compiler
{

    /**
     * Compiles a SELECT query.
     *
     * @access  public
     * @param   \Opis\Database\SQL\Query    $query  Query object.
     * @return  array
     */

    public function select(Query $query)
    {
        if($query->getLimit() === null)
        {
            // No limit so we can just execute a normal query
            return parent::select($query);
        }
        else
        {
            $sql  = $query->isDistinct() ? 'SELECT DISTINCT ' : 'SELECT ';
            $sql .= $this->columns($query->getColumns());
            $sql .= ' FROM ';
            $sql .= $this->wrap($query->getTable());
            $sql .= $this->joins($query->getJoins());
            $sql .= $this->wheres($query->getWheres());
            $sql .= $this->groupings($query->getGroupings());
            $sql .= $this->orderings($query->getOrderings());
            $sql .= $this->havings($query->getHavings());
            if($query->getOffset() === null)
            {
                // No offset so we only need a simple subquery to emulate the LIMIT clause
                $sql = 'SELECT m1.* FROM (' . $sql . ') m1 WHERE rownum <= ' . $query->getLimit();
            }
            else
            {
                // There is an offset so we need to make a bunch of subqueries to emulate the LIMIT and OFFSET clauses
                $limit  = $query->getLimit() + $query->getOffset();
                $offset = $query->getOffset() + 1;
                $sql = 'SELECT * FROM (SELECT m1.*, rownum AS opis_rownum FROM (' . $sql . ') m1 WHERE rownum <= ' . $limit . ') WHERE opis_rownum >= ' . $offset;
            }
            return array('sql' => $sql, 'params' => $this->params);
        }
    }
}