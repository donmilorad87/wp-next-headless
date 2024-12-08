"use client";
import React from "react";
import { Button } from "../button";

import Image from "next/image";
import { useToast } from "@/hooks/use-toast";
import { logout } from "@/lib/session";
const LogoutButton = ({ session }: { session: boolean }) => {
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
        <Button className="small-medium btn-secondary min-h-[41px] w-full rounded-lg px-4 py-3 shadow-bibe" onClick={logoutSubmit}>
          <Image src="/icons/logout.svg" alt="Logout" title="Logout" width={20} height={20} className="invert-colors lg:hidden" />
          <span className="primary-text-gradient max-lg:hidden">Sign Out</span>
        </Button>
      )}
    </>
  );
};

export default LogoutButton;
