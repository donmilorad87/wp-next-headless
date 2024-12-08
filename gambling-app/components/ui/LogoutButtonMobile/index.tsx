"use client";
import React from "react";
import { Button } from "../button";

import { useToast } from "@/hooks/use-toast";
import { logout } from "@/lib/session";
import { SheetClose } from "../sheet";
import Image from "next/image";
const LogoutButtonMobile = ({ session }: { session: boolean }) => {
  const { toast } = useToast();
  const logoutSubmit = async () => {
    toast({
      title: "Logout successful",
      description: "You have been logged out successfully",
    });
    await logout();
  };
  return (
    <>
      {session && (
        <SheetClose asChild>
          <Button className="small-medium btn-secondary min-h-[41px] w-full rounded-lg px-4 py-3 shadow-bibe" onClick={logoutSubmit}>
            <Image src="/icons/logout.svg" alt="Logout" title="Logout" width={20} height={20} className="invert-colors lg:hidden" />
            <span className="text-dark300_light900 flex items-center justify-start gap-4 bg-transparent p-4">Sign Out</span>
          </Button>
        </SheetClose>
      )}
    </>
  );
};

export default LogoutButtonMobile;
