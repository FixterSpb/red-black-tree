<?php

const COLOR_BLACK = "#000";
const COLOR_RED = "#c33";

class Node implements JsonSerializable
{
    private ?Node $parent = null;
    private ?Node $left = null;
    private ?Node $right = null;
    private ?int $value = null;
    private string $color = COLOR_BLACK;

    public function __construct(?int $value = null)
    {
        $this->value = $value;
    }

    public function add(?Node $node)
    {
        if (!$node->value) return;
        if ($node->value > $this->value) {
            if ($this->right && $this->right->value) {
                $this->right->add($node);
            } else {
                $this->right = $node;
                $this->setNodes($node);
            }
        } else {
            if ($this->left && $this->left->value) {
                $this->left->add($node);
            } else {
                $this->left = $node;
                $this->setNodes($node);
            }
        }
    }

    public function jsonSerialize(): array
    {
        return [
            "value" => $this->value,
            "color" => $this->color,
            "left" => $this->left,
            "right" => $this->right
        ];
    }

    public function isRed(): bool
    {
        return $this->color === COLOR_RED;
    }

    public function isBlack(): bool
    {
        return $this->color === COLOR_BLACK;
    }

    private function getUncle(): ?Node
    {
        if ($this->parent && $this->parent->parent) {
            if ($this->parent->parent->left === $this->parent) return $this->parent->parent->right;
            else return $this->parent->parent->left;
        }
        return null;
    }

    private function isLeftChild(): bool
    {
        if (!$this->parent) return false;
        return $this === $this->parent->left;
    }

    private function isRightChild(): bool
    {
        if (!$this->parent) return false;
        return $this === $this->parent->right;
    }

    private function control()
    {
        if (!$this->parent) return;
        if (!$uncle = $this->getUncle()) return;
        if ($this->isRed() && $this->parent->isRed() && $uncle->isRed()) {
            $this->parent->color = COLOR_BLACK;
            $uncle->color = COLOR_BLACK;
            if ($this->parent->parent->parent){
                $this->parent->parent->color = COLOR_RED;
            }else{
                $this->parent->parent->color = COLOR_BLACK;
            }
        } else if ($this->isRed() && $this->parent->isRed()) {
            if ($this->isLeftChild() && $this->parent->isRightChild()){
                $this->parent->rotateRight();
            }else if ($this->isRightChild() && $this->parent->isLeftChild()){
                $this->parent->rotateLeft();
            }else if($this->isLeftChild() && $this->parent->isLeftChild()){
                $this->parent->parent->rotateRight();
            }else if($this->isRightChild() && $this->parent->isRightChild()){
                $this->parent->parent->rotateLeft();
            }
        }
//        $this->parent->control();
    }

    public function rotateLeft(){
        $right = $this->right;
        $right->parent = $this->parent;
        $this->right = $right->left;
        if ($this->parent->left === $this) $this->parent->left = $right;
        else $this->parent->right = $right;
        $this->parent = $right;
        $right->left = $this;
        if ($this->isBlack() && $this->parent->isRed()){
            $this->color = COLOR_RED;
            $this->parent->color = COLOR_BLACK;
        }
        $this->control();
    }

    public function rotateRight(){
        $left = $this->left;
        $left->parent = $this->parent;
        $this->left = $left->right;
        if ($this->parent->left === $this) $this->parent->left = $left;
        else $this->parent->right = $left;
        $this->parent = $left;
        $left->right = $this;
        if ($this->isBlack() && $this->parent->isRed()){
            $this->color = COLOR_RED;
            $this->parent->color = COLOR_BLACK;
        }
        $this->control();
    }


    private function setNodes(Node $node)
    {
        $node->parent = $this;
        $node->left = new Node();
        $node->right = new Node();
        $node->color = COLOR_RED;
        $node->control();
    }
}