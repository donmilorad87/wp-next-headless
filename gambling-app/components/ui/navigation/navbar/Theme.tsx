"use client";

import * as React from "react";
import { Moon, Sun } from "lucide-react";
import { useTheme } from "next-themes";

import { Button } from "@/components/ui/button";
import { DropdownMenu, DropdownMenuContent, DropdownMenuItem, DropdownMenuTrigger } from "@/components/ui/dropdown-menu";

const Theme = () => {
  const { setTheme } = useTheme();

  return (
    <div style={{ zIndex: "999" }}>
      <DropdownMenu>
        <DropdownMenuTrigger asChild className="primary-gradient">
          <Button variant="outline" size="icon">
            <Sun className="h-[1.2rem] w-[1.2rem] rotate-0 scale-100 transition-all dark:-rotate-90 dark:scale-0" />
            <Moon className="absolute h-[1.2rem] w-[1.2rem] rotate-90 scale-0 transition-all dark:rotate-0 dark:scale-100" />
            <span className="sr-only">Toggle theme</span>
          </Button>
        </DropdownMenuTrigger>
        <DropdownMenuContent className="background-light900_dark300 border-none" align="end">
          <DropdownMenuItem className="primary-gradient rounded-lg text-light-900" onClick={() => setTheme("light")}>
            Light
          </DropdownMenuItem>
          <DropdownMenuItem className="primary-gradient rounded-lg text-light-900 my-2" onClick={() => setTheme("dark")}>
            Dark
          </DropdownMenuItem>
          <DropdownMenuItem className="primary-gradient rounded-lg text-light-900" onClick={() => setTheme("system")}>
            System
          </DropdownMenuItem>
        </DropdownMenuContent>
      </DropdownMenu>
    </div>
  );
};

export default Theme;
