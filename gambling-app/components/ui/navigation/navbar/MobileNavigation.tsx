"use client";
import React from "react";
import { Sheet, SheetClose, SheetContent, SheetDescription, SheetHeader, SheetTitle, SheetTrigger } from "@/components/ui/sheet";
import Image from "next/image";
import Link from "next/link";

import NavLinks from "./NavLinks";

import LoginButtonMobile from "../../LoginButtonMobile";
import LogoutButtonMobile from "../../LogoutButtonMobile";
const MobileNavigation = ({ session }: { session: boolean }) => {
  return (
    <Sheet>
      <SheetTrigger asChild>
        <Image src="/icons/hamburger.svg" width={36} height={36} alt="Menu" className="invert-colors sm:hidden" />
      </SheetTrigger>
      <SheetContent side="left" className="background-light900_dark200 border-none">
        <SheetTitle className="hidden">Navigation</SheetTitle>
        <Link href="/" className="flex items-center gap-1">
          <Image src="/images/gambling_com_group_logo.jpeg" width={23} height={23} alt="Gambling Logo" />
        </Link>
        <div className="no-scrollbar flex h-[calc(100vh-80px)] flex-col justify-between overfllow-y-scroll">
          <SheetClose asChild>
            <section className="flex h-full flex-col gap-6 pt-16">
              <NavLinks isMobileNav />
            </section>
          </SheetClose>
          <div className="flex flex-col gap-3">
            <LoginButtonMobile session={session} />
            <LogoutButtonMobile session={session} />
          </div>
        </div>
      </SheetContent>
    </Sheet>
  );
};

export default MobileNavigation;
