<?php
/* ===========================================================================
 * Copyright 2013-2016 The Opis Project
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

namespace Opis\Database\Schema;

class CreateTable
{
    /** @var    array */
    protected $columns = [];

    /** @var    mixed */
    protected $primaryKey;

    /** @var    array */
    protected $uniqueKeys = [];

    /** @var    array */
    protected $indexes = [];

    /** @var    array */
    protected $foreignKeys = [];

    /** @var    string */
    protected $table;

    /** @var    mixed */
    protected $engine;

    /** @var    mixed */
    protected $autoincrement;

    /**
     * Constructor
     *
     * @param   string $table
     */
    public function __construct($table)
    {
        $this->table = $table;
    }

    /**
     * @param   string $name
     * @param   string $type
     *
     * @return  CreateColumn
     */
    protected function addColumn($name, $type)
    {
        $column = new CreateColumn($this, $name, $type);
        $this->columns[$name] = $column;
        return $column;
    }

    /**
     * @return  string
     */
    public function getTableName()
    {
        return $this->table;
    }

    /**
     * @return  array
     */
    public function getColumns()
    {
        return $this->columns;
    }

    /**
     * @return  mixed
     */
    public function getPrimaryKey()
    {
        return $this->primaryKey;
    }

    /**
     * @return  array
     */
    public function getUniqueKeys()
    {
        return $this->uniqueKeys;
    }

    /**
     * @return  array
     */
    public function getIndexes()
    {
        return $this->indexes;
    }

    /**
     * @return  array
     */
    public function getForeignKeys()
    {
        return $this->foreignKeys;
    }

    /**
     * @return  mixed
     */
    public function getEngine()
    {
        return $this->engine;
    }

    /**
     * @return  mixed
     */
    public function getAutoincrement()
    {
        return $this->autoincrement;
    }

    /**
     * @param   string $name
     *
     * @return  $this
     */
    public function engine($name)
    {
        $this->engine = $name;
        return $this;
    }

    /**
     * @param string|string[] $columns
     * @param string|null $name
     * @return $this
     */
    public function primary($columns, string $name = null)
    {
        if (!is_array($columns)) {
            $columns = [$columns];
        }

        if ($name === null) {
            $name = $this->table . '_pk_' . implode('_', $columns);
        }

        $this->primaryKey = [
            'name' => $name,
            'columns' => $columns,
        ];

        return $this;
    }

    /**
     * @param string|string[] $columns
     * @param string|null $name
     * @return $this
     */
    public function unique($columns, string $name = null)
    {
        if (!is_array($columns)) {
            $columns = [$columns];
        }

        if ($name === null) {
            $name = $this->table . '_uk_' . implode('_', $columns);
        }

        $this->uniqueKeys[$name] = $columns;

        return $this;
    }

    /**
     * @param string|string[] $columns
     * @param string|null $name
     * @return $this
     */
    public function index($columns, string $name = null)
    {
        if (!is_array($columns)) {
            $columns = [$columns];
        }

        if ($name === null) {
            $name = $this->table . '_ik_' . implode('_', $columns);
        }

        $this->indexes[$name] = $columns;

        return $this;
    }

    /**
     * @param string|string[] $columns
     * @param string|null $name
     * @return ForeignKey
     */
    public function foreign($columns, string $name = null)
    {
        if (!is_array($columns)) {
            $columns = [$columns];
        }

        if ($name === null) {
            $name = $this->table . '_fk_' . implode('_', $columns);
        }

        return $this->foreignKeys[$name] = new ForeignKey($columns);
    }

    /**
     * @param   CreateColumn $column
     * @param   string|null $name
     *
     * @return  $this
     */
    public function autoincrement(CreateColumn $column, string $name = null)
    {
        if ($column->getType() !== 'integer') {
            return $this;
        }

        $this->autoincrement = $column->set('autoincrement', true);
        return $this->primary($column->getName(), $name);
    }

    /**
     * @param   string $name
     *
     * @return  CreateColumn
     */
    public function integer($name)
    {
        return $this->addColumn($name, 'integer');
    }

    /**
     * @param   string $name
     *
     * @return  CreateColumn
     */
    public function float($name)
    {
        return $this->addColumn($name, 'float');
    }

    /**
     * @param   string $name
     *
     * @return  CreateColumn
     */
    public function double($name)
    {
        return $this->addColumn($name, 'double');
    }

    /**
     * @param   string $name
     * @param   int|null $length (optional)
     * @param   int|null $precision (optional)
     *
     * @return  CreateColumn
     */
    public function decimal($name, $length = null, $precision = null)
    {
        return $this->addColumn($name, 'decimal')->length($length)->set('precision', $precision);
    }

    /**
     * @param   string $name
     *
     * @return  CreateColumn
     */
    public function boolean($name)
    {
        return $this->addColumn($name, 'boolean');
    }

    /**
     * @param   string $name
     *
     * @return  CreateColumn
     */
    public function binary($name)
    {
        return $this->addColumn($name, 'binary');
    }

    /**
     * @param   string $name
     * @param   int $length (optional)
     *
     * @return  CreateColumn
     */
    public function string($name, $length = 255)
    {
        return $this->addColumn($name, 'string')->length($length);
    }

    /**
     * @param   string $name
     * @param   int $length (optional)
     *
     * @return  CreateColumn
     */
    public function fixed($name, $length = 255)
    {
        return $this->addColumn($name, 'fixed')->length($length);
    }

    /**
     * @param   string $name
     *
     * @return  CreateColumn
     */
    public function text($name)
    {
        return $this->addColumn($name, 'text');
    }

    /**
     * @param   string $name
     *
     * @return  CreateColumn
     */
    public function time($name)
    {
        return $this->addColumn($name, 'time');
    }

    /**
     * @param   string $name
     *
     * @return  CreateColumn
     */
    public function timestamp($name)
    {
        return $this->addColumn($name, 'timestamp');
    }

    /**
     * @param   string $name
     *
     * @return  CreateColumn
     */
    public function date($name)
    {
        return $this->addColumn($name, 'date');
    }

    /**
     * @param   string $name
     *
     * @return  CreateColumn
     */
    public function dateTime($name)
    {
        return $this->addColumn($name, 'dateTime');
    }

    /**
     * Add soft delete column
     */
    public function softDelete()
    {
        $this->dateTime('deleted_at');
    }

    /**
     * Add timestamp columns
     */
    public function timestamps()
    {
        $this->dateTime('created_at')->notNull();
        $this->dateTime('updated_at');
    }

}
