"use client";

import { ArrowLeft } from "lucide-react";
import { useRouter } from "next/navigation";

import { Button } from "@/components/ui/button";

const BackButton = () => {
  const router = useRouter();
  return (
    <Button onClick={() => router.push("/")}>
      <ArrowLeft /> Back to Home
    </Button>
  );
};

export default BackButton;
